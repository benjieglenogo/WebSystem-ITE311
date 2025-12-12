<?php

if (!function_exists('format_bytes')) {
    /**
     * Format bytes to human-readable format
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    function format_bytes($bytes, $decimals = 2) {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        if ($factor == 0) {
            return $bytes . ' ' . $size[$factor];
        }

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
    }
}

if (!function_exists('get_file_icon')) {
    /**
     * Get Bootstrap Icons class for file type
     *
     * @param string $fileType
     * @return array
     */
    function get_file_icon($fileType) {
        $fileType = strtolower($fileType);
        $iconClass = 'bi bi-file-earmark';
        $iconColor = 'text-primary';

        switch ($fileType) {
            case 'pdf': $iconClass = 'bi bi-filetype-pdf'; $iconColor = 'text-danger'; break;
            case 'doc':
            case 'docx': $iconClass = 'bi bi-filetype-docx'; $iconColor = 'text-primary'; break;
            case 'ppt':
            case 'pptx': $iconClass = 'bi bi-filetype-pptx'; $iconColor = 'text-warning'; break;
            case 'zip':
            case 'rar': $iconClass = 'bi bi-filetype-zip'; $iconColor = 'text-info'; break;
            case 'jpg':
            case 'jpeg':
            case 'png': $iconClass = 'bi bi-filetype-img'; $iconColor = 'text-success'; break;
            case 'txt': $iconClass = 'bi bi-filetype-txt'; $iconColor = 'text-secondary'; break;
        }

        return [$iconClass, $iconColor];
    }
}
