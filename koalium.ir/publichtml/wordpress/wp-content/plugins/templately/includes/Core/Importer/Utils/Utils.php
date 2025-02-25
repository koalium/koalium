<?php

namespace Templately\Core\Importer\Utils;

use Exception;
use Templately\Core\Importer\WPImport;
use Templately\Utils\Base;

class Utils extends Base {


	/**
	 * @throws Exception
	 */
	public static function read_json_file( $path ) {
		if ( ! file_exists( $path ) ) {
			throw new Exception( __( 'JSON file not exists. ' . basename( $path ), 'templately' ) );
		}

		$file_content = self::file_get_contents( $path );

		return $file_content ? json_decode( $file_content, true ) : [];
	}

	/**
	 * @param $file
	 * @param mixed ...$args
	 *
	 * @return false|string
	 */
	public static function file_get_contents( $file, ...$args ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			return false;
		}

		return file_get_contents( $file, ...$args );
	}

	public static function get_builtin_wp_post_types(): array {
		$post_type_args = [
			'show_in_nav_menus' => true,
			'public'            => true
		];
		$_post_types    = get_post_types( $post_type_args, 'objects' );

		return array_merge( array_keys( $_post_types ), [ 'nav_menu_item', 'wp_navigation' ] );
	}

	public static function map_old_new_post_ids( array $imported_data ) {
		$result = [];

		$result += $imported_data['templates']['succeed'] ?? [];

		if ( isset( $imported_data['content'] ) ) {
			foreach ( $imported_data['content'] as $post_type ) {
				$result += $post_type['succeed'] ?? [];
			}
		}

		if ( isset( $imported_data['wp-content'] ) ) {
			foreach ( $imported_data['wp-content'] as $post_type ) {
				$result += $post_type['succeed'] ?? [];
			}
		}

		return $result;
	}

	public static function map_old_new_term_ids( array $imported_data ) {
		$result = [];

		if ( isset( $imported_data['terms'] ) ) {
			foreach ( $imported_data['terms'] as $post_type ) {
				$result += $post_type['succeed'] ?? [];
			}
		}

		return $result;
	}

	public static function map_old_new_term_ids_el( array $imported_data ): array {
		$result = [];

		if ( ! isset( $imported_data['taxonomies'] ) ) {
			return $result;
		}

		foreach ( $imported_data['taxonomies'] as $post_type_taxonomies ) {
			foreach ( $post_type_taxonomies as $taxonomy ) {
				foreach ( $taxonomy as $term ) {
					$result[ $term['old_id'] ] = $term['new_id'];
				}
			}
		}

		return $result;
	}

	/**
	 * @param string $platform
	 *
	 * @return ImportHelper
	 */
	public static function get_json_helper( string $platform ) {
		return $platform === 'elementor' ? new ElementorHelper() : new GutenbergHelper();
	}

	public static function get_backup_options() {
		global $wpdb;

		$prefix = '__templately_';
		$table_name = $wpdb->options; // Assuming default options table name

		$sql = "SELECT option_name, option_value FROM {$table_name} WHERE option_name LIKE %s";
		$prepared_sql = $wpdb->prepare($sql, array("$prefix%")); // Escape wildcard for security

		$results = $wpdb->get_results($prepared_sql);

		$templately_options = array();
		foreach ($results as $row) {
			$name = str_replace($prefix, '', $row->option_name);
			$templately_options[$name] = maybe_unserialize($row->option_value);
		}

		return $templately_options;
	}

	public static function backup_option_value($key, $autoload = 'no') {
		$old_value = get_option($key);
		if ($old_value) {
			update_option("__templately_$key", $old_value, $autoload);
		}
		else {
			add_option("__templately_$key", $old_value, '', $autoload);
		}
	}

	public static function update_option($key, $value, $autoload = 'no') {
		self::backup_option_value($key, $autoload);
		return update_option($key, $value, $autoload);
	}

	public static function import_page_settings( $id, $settings ) {
		$extra_settings = [
			'page_on_front' => [
				'show_on_front' => 'page'
			]
		];
		if ( isset( $settings['page_for_posts'] ) && $settings['page_for_posts'] ) {
			self::update_option( 'page_for_posts', $id );
		}
		if ( isset( $settings['show_on_front'] ) && $settings['show_on_front'] ) {
			self::update_option( 'page_on_front', $id );
			self::update_option( 'show_on_front', 'page' );
		}
		if ( ! empty( $settings['page_settings'] ) ) {
			foreach ( $settings['page_settings'] as $option_name => $val ) {
				self::update_option( $option_name, $id );
				if ( array_key_exists( $option_name, $extra_settings ) ) {
					foreach ( $extra_settings[ $option_name ] as $name => $value ) {
						self::update_option( $name, $value );
					}
				}
			}
		}
	}

	public static function upload_logo($url) {
		if(empty($url)) {
			return ['error' => __('URL is empty', 'templately')];
		}

		// Validate URL and ensure scheme is present
		if ( ! wp_http_validate_url( $url ) || !parse_url( $url, PHP_URL_SCHEME ) ) {
			return ['error' => __('Invalid URL', 'templately')];
		}

		$post_data     = self::prepare_post_data($url);
		$wp_importer   = new WPImport( null, ['fetch_attachments' => true] );
		$attachment_id = $wp_importer->process_attachment($post_data, $url);

		if(is_wp_error($attachment_id)){
			return ['error' => $attachment_id->get_error_message()];
		}

		return [
			'id'  => (int) $attachment_id,
			'url' => esc_url_raw(wp_get_attachment_url($attachment_id)),
		];
	}

    /**
     * Inserts a template into the Gutenberg editor.
     *
     * @param mixed $data
     * @param int $postId
     * @return array
     */
    public static function import_and_replace_attachments($content, $postId = 0) {
        // Instantiate GutenbergHelper
        $helper = new GutenbergHelper();

		$data = [
			'content' => $content,
		];

        // Organize URLs from the content
        $organizedUrls = $helper->parse_images($data['content']);
		if(empty($organizedUrls)){
			return $content;
		}

        // Define template settings
        $template_settings = [
            'post_id'       => $postId,
            '__attachments' => $organizedUrls,
        ];

        // Map post IDs and disable logging
        $helper->map_post_ids[$postId] = $postId;
        $helper->shouldLog = false;

        // Prepare the helper with the data and settings
        $helper->prepare($data, $template_settings);

        // Update the content in the data array
        $content = wp_unslash($helper->get_content());

        return $content;
    }

	public static function prepare_post_data($image_url, $post_parent = null, $logger = null) {
		$filetype = wp_check_filetype(basename($image_url));
		if (!$filetype['type']) {
			if(is_callable($logger)){
				// call the logger function
				call_user_func($logger, 'prepare', 'Error: Unable to determine the file type.', -1, 'eventLog');
			}
			return null;
		}

		$post_data = array(
			'post_title'     => basename($image_url),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_mime_type' => $filetype['type'],
			'guid'           => $image_url,
		);

		if($post_parent){
			$post_data['post_parent'] = $post_parent; // Set the parent post
		}

		if (preg_match('%wp-content/uploads/([0-9]{4}/[0-9]{2})%', $image_url, $matches)) {
			$post_data['upload_date'] = $matches[1];
		}
		else{
			$post_data['upload_date'] = date('Y/m');
		}

		return $post_data;
	}

}