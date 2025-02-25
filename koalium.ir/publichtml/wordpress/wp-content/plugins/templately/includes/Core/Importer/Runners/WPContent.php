<?php

namespace Templately\Core\Importer\Runners;

use Templately\Core\Importer\Utils\Utils;
use Templately\Core\Importer\WPImport;
use Templately\Utils\Helper;

class WPContent extends BaseRunner {
	protected $selected_types = [];

	/**
	 * @var int
	 */
	protected $total = 1;

	protected $processed = [];

	public function get_name(): string {
		return 'wp-content';
	}

	public function get_label(): string {
		return __( 'WordPress Contents', 'templately' );
	}

	public function log_message(): string {
		return __( 'Importing Content (Pages, Posts, Products, Navigation)', 'templately' );
	}

	public function should_run( $data, $imported_data = [] ): bool {
		return ! empty( $this->manifest['wp-content'] );
		// return isset( $data[ 'wp-content' ] );
	}

	public function import( $data, $imported_data ): array {
		$path       = $this->dir_path . 'wp-content' . DIRECTORY_SEPARATOR;
		$post_types = $this->filter_post_types( $data['selected_post_types'] ?? [] );
		$taxonomies = [];
		$terms      = [];
		$results    = [
			"wp-content" => $data["imported_data"]["wp-content"] ?? [],
			"terms"      => $data["imported_data"]["terms"] ?? [],
		];

		$this->import_actions();

		$contents        = $imported_data['wp-content_manifest_content'] ?? $this->manifest['wp-content'];
		$this->processed = $imported_data['wp-content_processed'] ?? [];
		$this->total = array_reduce( $contents, function ( $carry, $item ) {
			return $carry + count( $item );
		}, 0 );

		$processed_templates = $this->origin->get_progress();

		if(empty($processed_templates)){
			$this->log( 0 );
			$processed_templates = ["__started__"];
			$this->origin->update_progress( $processed_templates, ['wp-content_manifest_content' => $contents]);
		}

		foreach ( $post_types as $type ) {
			if (in_array($type, $processed_templates)) {
				continue;
			}

			if(empty($data['import_demo_content']) && !in_array($type, ['wp_navigation', 'nav_menu_item'])) {
				continue;
			}
			$import = $this->import_type_data( $type, $path, $imported_data, $taxonomies, $terms );

			if ( empty( $import['posts'] ) ) {
				continue;
			}
			if(isset($import['posts']['__attachments'])){
				$results['wp-content']['__attachments'][ $type ] = $import['posts']['__attachments'];
				unset($import['posts']['__attachments']);
			}
			$results['wp-content'][ $type ] = $import['posts'];
			$results['terms'][ $type ]      = $import['terms'];
			$imported_data                  = array_merge( $imported_data, $results );

			// Add the template to the processed templates and update the session data
			$processed_templates[] = $type;
			$this->origin->update_progress( $processed_templates, $results);

			// If it's not the last item, send the SSE message and exit
			// if( end($post_types) !== $type) {
			// 	$this->sse_message( [
			// 		'type'    => 'continue',
			// 		'action'  => 'continue',
			// 		'results' => __METHOD__ . '::' . __LINE__,
			// 	] );
			// 	exit;
			// }
		}

		$this->import_actions( true );

		return $results;
	}

	protected function filter_post_types( $selected_custom_post_types = [] ) {
		$wp_builtin_post_types = Utils::get_builtin_wp_post_types();

		foreach ( $selected_custom_post_types as $custom_post_type ) {
			if ( post_type_exists( $custom_post_type ) ) {
				$this->selected_types[] = $custom_post_type;
			}
		}

		$post_types      = array_merge( $wp_builtin_post_types, $this->selected_types );
		$index           = array_search( 'nav_menu_item', $post_types, true );
		$gutenberg_index = array_search( 'wp_navigation', $post_types, true );

		if ( false !== $index ) {
			unset( $post_types[ $index ] );
			$post_types[] = 'nav_menu_item';
		}

		if ( false !== $gutenberg_index ) {
			unset( $post_types[ $gutenberg_index ] );
			$post_types[] = 'wp_navigation';
		}

		return $post_types;
	}

	protected function _import_type_data( $type, $path, $imported_data, $taxonomies, $terms ): array {
		$args = [
			'fetch_attachments' => true,
			'origin'            => $this->origin,
			'json'              => $this->json,
			'posts'             => Utils::map_old_new_post_ids( $imported_data ),
			'terms'             => Utils::map_old_new_term_ids( $imported_data ),
			'taxonomies'        => ! empty( $taxonomies[ $type ] ) ? $taxonomies[ $type ] : [],
			'posts_meta'        => [
				self::META_SESSION_KEY => $this->session_id,
			],
			'terms_meta'        => [
				self::META_SESSION_KEY => $this->session_id,
			],
		];

		if ( isset( $imported_data['archive_settings'] ) ) {
			foreach ($imported_data['archive_settings'] as $key => $archive_settings) {
				$args['posts'][ $archive_settings['old_id'] ] = $archive_settings['page_id'];
				$args['posts'][ $archive_settings['archive_id'] ] = $archive_settings['page_id'];
			}
		}

		$file = $path . $type . '/' . $type . '.xml';

		if ( ! file_exists( $file ) ) {
			return [];
		}

		$wp_importer = new WPImport( $file, $args );
		$result      = $wp_importer->run();

		return $result;
	}

	protected function import_type_data( $type, $path, $imported_data, $taxonomies, $terms ): array {
		$result = $this->_import_type_data( $type, $path, $imported_data, $taxonomies, $terms );
		if(isset($result['summary'])){
			return $result['summary'];
		}
		return [];
	}

	protected function import_actions( $remove = false ) {
		if ( ! $remove ) {
			add_action( 'templately_import.process_post', [ $this, 'post_log' ], 10, 2 );
			add_action( 'templately_import.process_term', [ $this, 'post_log' ], 10, 2 );
			add_action( 'import_start', [ $this, 'update_total' ], 10 );

			return;
		}

		remove_action( 'templately_import.process_post', [ $this, 'post_log' ] );
		remove_action( 'templately_import.process_term', [ $this, 'post_log' ] );
		remove_action( 'import_start', [ $this, 'update_total' ] );
	}

	public function post_log( $post, $result ) {
		if ( isset( $post['post_type'] ) ) {
			if ( ! isset( $this->manifest['wp-content'][ $post['post_type'] ] ) ) {
				return;
			}

			$commonItems = array_intersect(array_keys($result['succeed']), $this->manifest['wp-content'][ $post['post_type'] ]);
			$this->processed[ $post['post_type'] ] = count($commonItems);

			$type  = $post['post_type'];
			$title = $post['post_title'];

			$this->origin->update_progress( null, ['wp-content_processed' => $this->processed]);
		} elseif ( isset( $post['term_id'] ) ) {
			/**
			 * FIXME: We should fix it later, with a proper count of terms and make a total with post itself.
			 */
			return;
		}

		if ( empty( $type ) || empty( $title ) ) {
			return;
		}

		$failed = 0;
		if ( ! empty( $result['failed'] ) ) {
			$failed = count( $result['failed'] );
		}

		$processed = array_reduce( $this->processed, function ( $carry, $item ) {
			return $carry + $item;
		}, 0 );

		$progress = $this->total > 0 ? floor( ( 100 * ( $processed + $failed ) ) / $this->total ) : 100;

		$this->log( $progress);
	}

	public function update_total( $WPImport ) {
		$session_data = $this->origin->get_session_data();
		$contents     = $session_data['imported_data']['wp-content_manifest_content'];

		foreach ($WPImport->posts as $key => $post) {
			$postType = $post['post_type'];
			$postId = $post['post_id'];

			if (!isset($contents[$postType]) || !in_array($postId, $contents[$postType])) {
				$contents[$postType][] = $postId;
			}
		}

		$this->total = array_reduce( $contents, function ( $carry, $item ) {
			return $carry + count( $item );
		}, 0 );

		$this->origin->update_progress( null, ['wp-content_manifest_content' => $contents]);
	}
}