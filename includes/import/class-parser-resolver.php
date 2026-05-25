<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Import_Parser_Resolver
{
    public static function resolve(string $filepath, string $extension): FCManager_Import_Parser
    {
        $parsers = apply_filters('fcmanager_import_parsers', []);

        foreach ($parsers as $parser_class) {
            if (
                in_array($extension, $parser_class::supported_extensions())
                && $parser_class::supports($filepath)
            ) {
                return new $parser_class($filepath);
            }
        }

        /* translators: %s: File extension without dot (e.g. 'csv') */
        throw new Exception(esc_html(sprintf(__("No parser found for %s file", 'football-club-manager'), $extension)));
    }

    public static function supported_extensions(): array
    {
        $supported = [];
        $parsers = apply_filters('fcmanager_import_parsers', []);
        foreach ($parsers as $parser_class) {
            $extensions = $parser_class::supported_extensions();
            foreach ($extensions as $extension) {
                if (! in_array($extension, $supported)) {
                    $supported[] = $extension;
                }
            }
        }
        return $supported;
    }
}
