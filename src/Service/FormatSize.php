<?php

namespace App\Service;

class FormatSize
{
    function formatSizeUnits(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            // Convert to gigabytes
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            // Convert to megabytes
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            // Convert to kilobytes
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            // Display in bytes
            return $bytes . ' bytes';
        } elseif ($bytes === 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

}