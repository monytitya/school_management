<?php
class UploadHelper {
    public static function uploadImage($file, $directory = 'profiles') {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) return null;
        
        $targetDir = __DIR__ . '/../uploads/' . $directory . '/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $targetFile = $targetDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return 'uploads/' . $directory . '/' . $filename;
        }
        return null;
    }
}
