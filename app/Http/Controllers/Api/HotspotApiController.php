<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotspot;

class HotspotApiController extends Controller
{
    public function show($id)
    {
        $hotspot = Hotspot::with('items')
            ->where('id', $id)
            ->orWhere('heritage_slug', $id)
            ->orWhere('heritage_id', $id)
            ->first();

        if (!$hotspot) {
            return response()->json([
                'id' => $id,
                'heritageId' => $id,
                'hotspots' => [],
            ]);
        }

        return response()->json([
            'id' => $hotspot->id,
            'heritageId' => $hotspot->heritage_id,
            'heritageSlug' => $hotspot->heritage_slug,
            'title' => $hotspot->title,
            'hotspots' => $hotspot->items->map(function ($item) {
                return [
                    'id' => (string)$item->id,
                    'title' => $item->title,
                    'description' => $item->description ?? '',
                    'x' => (float)$item->x,
                    'y' => (float)$item->y,
                    'z' => (float)$item->z,
                ];
            }),
        ]);
    }
}
