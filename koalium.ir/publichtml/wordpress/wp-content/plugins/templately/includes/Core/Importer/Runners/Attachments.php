<?php

namespace Templately\Core\Importer\Runners;

use Exception;
use Templately\Core\Importer\Form;
use Templately\Core\Importer\Runners\BaseRunner;
use Templately\Core\Importer\Utils\Utils;
use Templately\Core\Importer\WPImport;

class Attachments extends WPContent {

	public function get_name(): string {
		return 'attachments';
	}

	public function get_label(): string {
		return __( 'Attachments', 'templately' );
	}

	public function should_log(): bool {
		return true;
	}

	public function get_action(): string {
		return 'updateLog';
	}

	public function log_message(): string {
		if($this->total > 0){
			return __( 'Importing Attachments: (' . $this->get_processed() . '/' . $this->total . ')', 'templately' );
		}
		return __( 'Importing Attachment.', 'templately' );
	}

	public function should_run( $data, $imported_data = [] ): bool {
		$params = $this->origin->get_request_params();
		return !empty($this->manifest['has_attachments']);
	}

	public function import( $data, $imported_data ): array {
		$path       = $this->dir_path;
		$taxonomies = [];
		$terms      = [];
		$results    = [];

		$this->import_actions();

		$import = $this->_import_type_data( 'attachments', $path, $imported_data, $taxonomies, $terms );
		$results['attachments'] = $import['summary']['posts'];
		$results['attachments_errors'] = $import['errors'];

		$this->import_actions( true );

		return $results;
	}

	protected function import_actions( $remove = false ) {
		parent::import_actions( $remove );

		if ( $remove ) {
			remove_filter( 'templately_import_copy_attachment', [ $this, 'copy_attachment_file' ], 10, 4 );
		} else {
			add_filter( 'templately_import_copy_attachment', [ $this, 'copy_attachment_file' ], 10, 4 );
		}
	}

	public function copy_attachment_file( $return, $att_id, $dest_file, $upload_dir ) {
		// @todo check mapping url to new id

		if(!empty($att_id)){
			$file_name   = basename($dest_file);
			$path        = $this->dir_path . 'attachments' . DIRECTORY_SEPARATOR;
			$wp_filetype = wp_check_filetype( $file_name);
			$ext         = $wp_filetype['ext'];

			$source_path = "{$path}{$att_id}.{$ext}";

			if(!file_exists($source_path)){
				return $return;
			}

			$move_new_file = copy( $source_path, $dest_file );

			if(!$move_new_file){
				return $return;
			}

			$wp_filetype = wp_check_filetype_and_ext( $source_path, $file_name );

			// Set correct file permissions.
			$stat  = stat( dirname( $dest_file ) );
			$perms = $stat['mode'] & 0000666;
			chmod( $dest_file, $perms );

			return [
				'file'  => $dest_file,
				'url'   => $upload_dir['url'] . "/$file_name",
				'type'  => $wp_filetype['type'],
				'error' => false,
			];
		}


		return $return;
	}

	public function get_processed() {
		$processed = $this->processed ?? [];
		$succeed = $processed['succeed'] ?? [];
		$failed = $processed['failed'] ?? [];

		return count($succeed) + count($failed);
	}

	public function post_log( $post, $result ) {
		if ( isset( $post['post_type'] ) ) {
			if ( $post['post_type'] !== 'attachment'  ) {
				return;
			}

			// need to assign to processed so log_message() can use get_processed() to calculate progress
			// also bellow code used it to calculate progress
			$this->processed = $result;

			$type  = $post['post_type'];
			$title = $post['post_title'];

		}

		if ( empty( $type ) || empty( $title ) ) {
			return;
		}

		$progress = $this->total > 0 ? ceil( ( 100 * ( $this->get_processed() ) ) / $this->total ) : 100;

		$this->log( $progress);
	}
}