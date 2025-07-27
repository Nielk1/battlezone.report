<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function show($path)
    {
        // Normalize slashes for Windows compatibility
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Check for public/*
        $publicFullPath = realpath(public_path($path));
        if (
            $publicFullPath !== false &&
            strpos($publicFullPath, public_path()) === 0 &&
            file_exists($publicFullPath)
        ) {
            //return response()->file($publicFullPath);
            return $this->imageResponse($publicFullPath);
        }

        // Check for resources/data/(articles|alternate)/*.(png|jpe?g|gif|webp)
        $allowedDirs = [
            resource_path('data' . DIRECTORY_SEPARATOR . 'articles'),
            resource_path('data' . DIRECTORY_SEPARATOR . 'issues'),
            resource_path('data' . DIRECTORY_SEPARATOR . 'chronicle'),
            resource_path('data' . DIRECTORY_SEPARATOR . 'team'),
        ];
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

        foreach ($allowedDirs as $baseDir) {
            $prefix = basename($baseDir); // 'articles' or 'issues'
            if (stripos($path, $prefix . DIRECTORY_SEPARATOR) === 0) {
                $relativePath = substr($path, strlen($prefix . DIRECTORY_SEPARATOR));
                $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $relativePath);
                $ext = strtolower(pathinfo($fullPath ?? '', PATHINFO_EXTENSION));
                if (
                    $fullPath !== false &&
                    strpos($fullPath, $baseDir) === 0 &&
                    file_exists($fullPath) &&
                    in_array($ext, $allowedExtensions)
                ) {
                    //return response()->file($fullPath);
                    return $this->imageResponse($fullPath);
                }
            }
        }

        abort(404);
    }

    /**
     * Return image response with additional headers
     */
    protected function imageResponse($filePath)
    {
        $mimeType = mime_content_type($filePath);
        $size = filesize($filePath);
        $lastModified = gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT';

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Length' => $size,
            'Last-Modified' => $lastModified,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
