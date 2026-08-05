<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class MediaApiController extends Controller
{
    public function serve($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $type = 'application/octet-stream';
        try {
            if (function_exists('mime_content_type')) {
                $type = @mime_content_type($fullPath) ?: 'application/octet-stream';
            }
        } catch (\Throwable $e) {}

        if (str_ends_with($fullPath, '.glb')) {
            $type = 'model/gltf-binary';
        } elseif (str_ends_with($fullPath, '.gltf')) {
            $type = 'model/gltf+json';
        } elseif (str_ends_with($fullPath, '.png')) {
            $type = 'image/png';
        } elseif (str_ends_with($fullPath, '.jpg') || str_ends_with($fullPath, '.jpeg')) {
            $type = 'image/jpeg';
        } elseif (str_ends_with($fullPath, '.webp')) {
            $type = 'image/webp';
        }

        return response()->file($fullPath, [
            'Content-Type' => $type,
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    }
}
