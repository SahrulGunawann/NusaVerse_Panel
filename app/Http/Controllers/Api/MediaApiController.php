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

        $type = mime_content_type($fullPath) ?: 'application/octet-stream';
        if (str_ends_with($fullPath, '.glb')) {
            $type = 'model/gltf-binary';
        } elseif (str_ends_with($fullPath, '.gltf')) {
            $type = 'model/gltf+json';
        }

        return response()->file($fullPath, [
            'Content-Type' => $type,
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    }
}
