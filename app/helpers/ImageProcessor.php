<?php

/**
 * Image Processing Helper Class
 * Handles image resizing, cropping, and optimization
 */

class ImageProcessor
{

    // Standard sizes for different contexts
    const PROFILE_SIZE = 300;      // Profile photos
    const THUMBNAIL_SIZE = 150;    // Thumbnails
    const AVATAR_SIZE = 80;        // Small avatars
    const HEADER_SIZE = 40;        // Header navigation

    private $quality = 90;         // JPEG quality (1-100) - increased for better quality
    private $maxFileSize = 2 * 1024 * 1024; // 2MB max after processing - reduced from 500KB
    private $maxUploadSize = 3 * 1024 * 1024; // 3MB max upload size - reduced from 5MB

    /**
     * Process uploaded image: resize, crop, and optimize
     */
    public function processUpload($file, $user_id, $targetSize = self::PROFILE_SIZE)
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Get image info
            $imageInfo = getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                throw new Exception("Invalid image file");
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];

            // Create image resource from uploaded file
            $sourceImage = $this->createImageFromFile($file['tmp_name'], $mimeType);

            // Calculate crop dimensions for square aspect ratio
            $cropSize = min($originalWidth, $originalHeight);
            $cropX = ($originalWidth - $cropSize) / 2;
            $cropY = ($originalHeight - $cropSize) / 2;

            // Create target image (square)
            $targetImage = imagecreatetruecolor($targetSize, $targetSize);

            // Enable alpha blending for PNG transparency
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);

            // Copy and resize image
            imagecopyresampled(
                $targetImage,
                $sourceImage,
                0,
                0,
                $cropX,
                $cropY,
                $targetSize,
                $targetSize,
                $cropSize,
                $cropSize
            );

            // Generate filename
            $extension = $this->getExtensionFromMime($mimeType);
            $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
            $filepath = PUBLIC_PATH . '/images/imageUsers/' . $filename;

            // Save optimized image
            $this->saveImage($targetImage, $filepath, $mimeType);

            // Clean up memory immediately
            imagedestroy($sourceImage);
            imagedestroy($targetImage);

            // Optimize file size if needed (but with higher limit now)
            $fileSize = filesize($filepath);
            if ($fileSize > $this->maxFileSize) {
                // If still too large, reduce quality more aggressively
                $this->optimizeFileSize($filepath, $mimeType, $fileSize);
            }

            return $filename;
        } catch (Exception $e) {
            throw new Exception("Image processing failed: " . $e->getMessage());
        }
    }

    /**
     * Create multiple sizes of an image
     */
    public function createMultipleSizes($sourceFile, $user_id)
    {
        $sizes = [
            'profile' => self::PROFILE_SIZE,
            'thumbnail' => self::THUMBNAIL_SIZE,
            'avatar' => self::AVATAR_SIZE,
            'header' => self::HEADER_SIZE
        ];

        $filenames = [];

        foreach ($sizes as $sizeName => $size) {
            try {
                $filename = $this->processUpload($sourceFile, $user_id . '_' . $sizeName, $size);
                $filenames[$sizeName] = $filename;
            } catch (Exception $e) {
                // Continue with other sizes if one fails
                error_log("Failed to create {$sizeName} size: " . $e->getMessage());
            }
        }

        return $filenames;
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $file['error']);
        }

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
        }

        if ($file['size'] > $this->maxUploadSize) {
            throw new Exception("File too large. Maximum size is " . ($this->maxUploadSize / (1024 * 1024)) . "MB.");
        }

        // Check if it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new Exception("Invalid image file - corrupted or not a real image.");
        }
    }

    /**
     * Create image resource from file
     */
    private function createImageFromFile($filepath, $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($filepath);
            case 'image/png':
                return imagecreatefrompng($filepath);
            case 'image/gif':
                return imagecreatefromgif($filepath);
            default:
                throw new Exception("Unsupported image type: " . $mimeType);
        }
    }

    /**
     * Save image to file
     */
    private function saveImage($image, $filepath, $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagejpeg($image, $filepath, $this->quality);
            case 'image/png':
                // PNG compression level (0-9)
                $pngQuality = floor((100 - $this->quality) / 10);
                return imagepng($image, $filepath, $pngQuality);
            case 'image/gif':
                return imagegif($image, $filepath);
            default:
                throw new Exception("Cannot save image type: " . $mimeType);
        }
    }

    /**
     * Get file extension from MIME type
     */
    private function getExtensionFromMime($mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            default:
                return 'jpg';
        }
    }

    /**
     * Reduce image quality if file is too large
     */
    private function reduceQuality($filepath, $mimeType)
    {
        if ($mimeType !== 'image/jpeg') {
            return; // Only reduce JPEG quality
        }

        $image = imagecreatefromjpeg($filepath);
        $newQuality = max(60, $this->quality - 20); // Reduce quality but not below 60

        imagejpeg($image, $filepath, $newQuality);
        imagedestroy($image);
    }

    /**
     * Get image dimensions
     */
    public function getImageDimensions($filepath)
    {
        $info = getimagesize($filepath);
        return [
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info['mime']
        ];
    }

    /**
     * Check if image needs processing
     */
    public function needsProcessing($file, $maxSize = self::PROFILE_SIZE)
    {
        $info = getimagesize($file['tmp_name']);
        $fileSize = $file['size'];

        return (
            $info[0] > $maxSize ||
            $info[1] > $maxSize ||
            $fileSize > $this->maxFileSize
        );
    }

    /**
     * Create a thumbnail from existing image
     */
    public function createThumbnail($sourcePath, $targetPath, $size = self::THUMBNAIL_SIZE)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new Exception("Cannot read source image");
        }

        $sourceImage = $this->createImageFromFile($sourcePath, $imageInfo['mime']);

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];

        // Calculate crop for square thumbnail
        $cropSize = min($originalWidth, $originalHeight);
        $cropX = ($originalWidth - $cropSize) / 2;
        $cropY = ($originalHeight - $cropSize) / 2;

        $thumbnail = imagecreatetruecolor($size, $size);

        imagecopyresampled(
            $thumbnail,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            $size,
            $size,
            $cropSize,
            $cropSize
        );

        $this->saveImage($thumbnail, $targetPath, $imageInfo['mime']);

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return true;
    }

    /**
     * Optimize file size by reducing quality
     */
    private function optimizeFileSize($filepath, $mimeType, $currentSize)
    {
        // Only optimize JPEG files for size
        if ($mimeType !== 'image/jpeg') {
            return;
        }

        $targetSize = $this->maxFileSize;
        $quality = $this->quality;

        // Reduce quality in steps until we reach acceptable file size
        while ($currentSize > $targetSize && $quality > 60) {
            $quality -= 10;

            // Reload and resave with lower quality
            $image = imagecreatefromjpeg($filepath);
            if ($image) {
                imagejpeg($image, $filepath, $quality);
                imagedestroy($image);

                $currentSize = filesize($filepath);
            } else {
                break;
            }
        }
    }
}
