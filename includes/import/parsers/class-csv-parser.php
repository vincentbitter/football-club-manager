<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../class-parser.php';

class FCManager_Import_Parsers_CSV_Parser extends FCManager_Import_Parser
{
    protected $headers;
    protected $rows;

    public function __construct($file_path)
    {
        global $wp_filesystem;
        WP_Filesystem();

        $content = $wp_filesystem->get_contents($file_path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', $content);

        if (empty($lines)) {
            return;
        }

        $delimiter = $this->detect_delimiter($lines[0]);

        $current_index = 0;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $data = str_getcsv($line, $delimiter, '"', '"');

            if ($current_index === 0) {
                $this->headers = $data;
            } else {
                $this->rows[] = $data;
            }

            $current_index++;
        }
    }

    public static function supports(string $filename): bool
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filename);

        return $mime === 'text/plain' || $mime === 'text/csv';
    }

    public function supported_extensions(): array
    {
        return ['csv'];
    }

    public function get_row(int $index)
    {
        if ($index < $this->get_total_rows())
            return $this->rows[$index];

        return null;
    }

    public function get_total_rows(): int
    {
        return count($this->rows);
    }

    protected function detect_delimiter(string $line): string
    {
        $delimiters = [',', ';', "\t", '|', "\x1e", "\x1f"];

        $bestDelimiter = ',';
        $bestCount = 0;

        foreach ($delimiters as $delim) {
            $count = substr_count($line, $delim);
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelimiter = $delim;
            }
        }

        return $bestDelimiter;
    }
}

add_filter('fcmanager_import_parsers', function ($parsers) {
    $parsers[] = FCManager_Import_Parsers_CSV_Parser::class;
    return $parsers;
});
