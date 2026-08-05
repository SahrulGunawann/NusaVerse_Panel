<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Heritage;
use Illuminate\Http\Request;

class HeritageApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Heritage::query();

        if ($request->filled('q')) {
            $search = strtolower($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(province_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(category_name) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $cat = $request->input('category');
            $query->where(function ($q) use ($cat) {
                $q->where('category_id', $cat)
                  ->orWhereRaw('LOWER(category_name) = ?', [strtolower($cat)]);
            });
        }

        if ($request->filled('province') && $request->input('province') !== 'all') {
            $prov = $request->input('province');
            $query->where(function ($q) use ($prov) {
                $q->where('province_id', $prov)
                  ->orWhereRaw('LOWER(province_name) = ?', [strtolower($prov)]);
            });
        }

        $heritages = $query->get()->map(function ($h) {
            return $this->formatHeritage($h);
        });

        return response()->json($heritages);
    }

    public function featured()
    {
        $heritages = Heritage::where('is_featured', true)->get()->map(function ($h) {
            return $this->formatHeritage($h);
        });

        return response()->json($heritages);
    }

    public function show($slug)
    {
        $heritage = Heritage::where('slug', $slug)->orWhere('id', $slug)->first();

        if (!$heritage) {
            return response()->json(['message' => 'Heritage not found'], 404);
        }

        return response()->json($this->formatHeritage($heritage));
    }

    private function formatHeritage(Heritage $heritage)
    {
        $request = request();
        $host = $request ? $request->getSchemeAndHttpHost() : 'http://192.168.100.7:8000';
        
        $coverImage = $heritage->cover_image;
        if (str_contains($coverImage, '/storage/')) {
            $path = \Illuminate\Support\Str::after($coverImage, '/storage/');
            $coverImage = $host . '/api/v1/media/' . ltrim($path, '/');
        } elseif (!str_starts_with($coverImage, 'http://') && !str_starts_with($coverImage, 'https://') && !str_starts_with($coverImage, 'assets/')) {
            $coverImage = $host . '/api/v1/media/' . ltrim($coverImage, '/');
        }

        $model3dUrl = $heritage->model_3d_url;
        $has3dModel = !empty($model3dUrl) && $model3dUrl !== 'none' && $model3dUrl !== 'assets/models/placeholder.glb';

        if ($has3dModel) {
            if (str_contains($model3dUrl, '/storage/')) {
                $path = \Illuminate\Support\Str::after($model3dUrl, '/storage/');
                $model3dUrl = $host . '/api/v1/media/' . ltrim($path, '/');
            } elseif (!str_starts_with($model3dUrl, 'http://') && !str_starts_with($model3dUrl, 'https://') && !str_starts_with($model3dUrl, 'assets/')) {
                $model3dUrl = $host . '/api/v1/media/' . ltrim($model3dUrl, '/');
            }
        } else {
            $model3dUrl = '';
        }

        $sources = $heritage->sources ?? [];
        if (empty($sources) && !empty($heritage->source_name)) {
            $sources = [
                ['name' => $heritage->source_name, 'url' => $heritage->source_url ?? '']
            ];
        }

        return [
            'id' => $heritage->id,
            'name' => $heritage->name,
            'slug' => $heritage->slug,
            'category' => $heritage->category_name ?? $heritage->category_id ?? 'Cagar Budaya',
            'province' => $heritage->province_name ?? $heritage->province_id ?? 'Indonesia',
            'shortDescription' => $heritage->short_description,
            'fullDescription' => $heritage->full_description,
            'additionalSections' => $heritage->additional_sections ?? [],
            'sourceName' => $heritage->source_name ?? '',
            'sourceUrl' => $heritage->source_url ?? '',
            'sources' => $sources,
            'coverImage' => $coverImage,
            'has3dModel' => $has3dModel,
            'model3dAsset' => $model3dUrl,
            'latitude' => (float)$heritage->latitude,
            'longitude' => (float)$heritage->longitude,
            'openingHours' => $heritage->opening_hours ?? '08.00 - 17.00 WIB',
            'ticketPrice' => $heritage->ticket_price ?? 'Gratis',
            'isFeatured' => (bool)$heritage->is_featured,
            'timelineId' => $heritage->timeline_id ?? 'timeline_' . $heritage->slug,
            'hotspotId' => $heritage->hotspot_id ?? 'hotspot_' . $heritage->slug,
        ];
    }
}
