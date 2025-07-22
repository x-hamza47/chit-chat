<?php

namespace App\Traits;

trait FileHelper {

    // ! Get file extension
    protected static function getExtension(String $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    // ! Create filename
    protected static function createFilename(String $extension): string
    {
        return bin2hex(random_bytes(8)) . '.' . $extension;
    }

    // ! Get Upload Path
    protected static function getUploadPath(string $filename): string
    {
        $uploadDir = __DIR__ . '/../../upload/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        return $uploadDir . $filename;
    }

    // ! Check file extension 
    protected static function isAllowedExtension(array $file): bool
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($file['tmp_name']);
        return in_array($fileType, $allowedTypes, true);
    }
}