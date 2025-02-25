<?php
namespace Templately\Core\Importer\Utils;

use Templately\Core\Importer\FullSiteImport;

class LogHandler {
    public static function get_log_dir() {
        $upload_dir = wp_upload_dir();
        $log_dir    = trailingslashit($upload_dir['basedir']) . 'templately' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR;
        return $log_dir;
    }

    public static function get_log_file_path() {
        $request_params = FullSiteImport::_get_session_data();
        $session_id = $request_params['session_id'];
        $log_dir = self::get_log_dir();
        return trailingslashit($log_dir) . "fsi-$session_id.log";
    }

    public static function create_log_dir() {
        $upload_dir = wp_upload_dir();
        $log_dir    = self::get_log_dir();

        if (!$log_dir) {
            return false;
        }

        if (!is_writable($upload_dir['basedir'])) {
            return false;
        }

        $data = ['log_type' => 'file'];
        FullSiteImport::_update_session_data($data);

        if (!is_dir($log_dir)) {
            wp_mkdir_p($log_dir);
        } else {
            $files = array_diff(scandir($log_dir), ['.', '..']);

            if (count($files) > 10) {
                usort($files, function($a, $b) use ($log_dir) {
                    return filemtime($log_dir . '/' . $a) - filemtime($log_dir . '/' . $b);
                });

                $files_to_delete = array_slice($files, 0, count($files) - 10);

                foreach ($files_to_delete as $file) {
                    unlink($log_dir . '/' . $file);
                }
            }
        }
    }

    public static function sse_log_file($log) {
        $file_path  = self::get_log_file_path();

        // Write to the log file
        return file_put_contents($file_path, json_encode($log) . PHP_EOL, FILE_APPEND);
    }

    public static function read_log_file($start_line = 0) {
        $file_path = self::get_log_file_path();

        $log = [];

        // Check if SplFileObject exists
        if (class_exists('SplFileObject')) {
            try {
                $file = new \SplFileObject($file_path);

                // Seek to the specified line number (0-indexed in SplFileObject)
                $file->seek($start_line);
                while (!$file->eof()) {
                    $line = trim($file->current());
                    if ($line) {
                        $log[] = json_decode($line, true); // JSON decode each line
                    }
                    $file->next();
                }
            } catch (\Exception $e) {
                // Handle exception if file operations fail
                error_log("Failed to read log file: " . $e->getMessage());
            }
        } else {
            // Fallback if SplFileObject doesn't exist
            $current_line = 1;
            $handle = fopen($file_path, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if ($current_line >= $start_line) {
                        $line = trim($line);
                        if ($line) {
                            $log[] = json_decode($line, true); // JSON decode each line
                        }
                    }
                    $current_line++;
                }
                fclose($handle);
            } else {
                // Handle error if file can't be opened
                error_log("Failed to open log file: $file_path");
            }
        }

        return $log;
    }
}
