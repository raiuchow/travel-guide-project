<?php
/**
 * Image Upload Service
 * SOLID: Single Responsibility - শুধুমাত্র image upload handle করে
 */

namespace App\Services;

use RuntimeException;

class ImageUploadService {
    private string $uploadDir;
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private int $maxSize = 5 * 1024 * 1024; // 5 MB

    public function __construct(string $uploadDir = __DIR__ . '/../../public/uploads/posts/') {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new RuntimeException("Failed to create upload directory.");
            }
        }
    }

    /**
     * Upload a single file
     * @return string Relative web path
     * @throws RuntimeException
     */
    public function upload(array $file): string {
        $this->validate($file);

        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('post_', true) . '.' . $ext;
        $target   = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        return 'public/uploads/posts/' . $filename;
    }

    /**
     * Validate uploaded file
     * @throws RuntimeException
     */
    private function validate(array $file): void {
        // Check upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload error code: ' . $file['error']);
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            throw new RuntimeException('File exceeds 5 MB limit.');
        }

        // Check MIME type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        
        if (!in_array($mime, $this->allowedTypes, true)) {
            throw new RuntimeException('Invalid file type. Allowed: JPEG, PNG, WebP, GIF.');
        }

        // Additional security: check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (!in_array($ext, $allowedExtensions, true)) {
            throw new RuntimeException('Invalid file extension.');
        }
    }

    /**
     * Delete uploaded file
     */
    public function delete(string $filePath): bool {
        $fullPath = __DIR__ . '/../../' . $filePath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
}
