<?php

namespace App\Utils;

use App\Traits\FileHelper;

class ImageUploader {
    
    use FileHelper;
    
    // ! Upload Image
    public static function upload(Array $file) :array 
    {
        if (empty($file['name'])) {
            return ['status' => false, 'message' => 'Please select an image file!'];
        }
        if (!self::isAllowedExtension($file)) {
            return ['status' => false, 'message' => 'Invalid image file type.'];
        }

        $extension = self::getExtension($file['name']);
        $new_name = self::createFilename($extension);
        $target = self::getUploadPath($new_name);

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['status' => false, 'message' => 'Image upload failed'];
        }

        return ['status' => true, 'filename' => $new_name];
    }

    // ! Unlink file
    public static function delete(string $filename): bool
    {
        $filePath = self::getUploadPath($filename);
        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        }
        return false;
    }
}