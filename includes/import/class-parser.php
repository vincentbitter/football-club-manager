<?php

if (! defined('ABSPATH')) {
    exit;
}

abstract class FCManager_Import_Parser
{
    /**
     * Determine if this parser supports the given file. 
     * The $file_path is provided so that parsers can inspect the file if needed 
     * (e.g. to check for a specific header row).
     * 
     * @param string $filename The name of the file being imported.
     * @return bool True if this parser can handle the file, false otherwise.
     */
    public abstract static function supports(string $filename): bool;

    /**
     * Get the supported file extensions for this parser.
     * 
     * @return array An array of supported file extensions.
     */
    public abstract function supported_extensions(): array;

    /**
     * Get a specific row of the import file as an associative array, where the keys are the column names.
     * 
     * @param int $index The zero-based index of the row to retrieve (not counting the header row).
     * @return array|null The row data as an associative array, or null if the index
     */
    public abstract function get_row(int $index);

    /**
     * Get the total number of rows in the import file.
     */
    public abstract function get_total_rows(): int;
}
