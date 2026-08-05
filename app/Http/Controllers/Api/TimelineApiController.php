<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timeline;

class TimelineApiController extends Controller
{
    public function show($id)
    {
        $timeline = Timeline::with('events')->where('id', $id)->orWhere('heritage_slug', $id)->orWhere('heritage_id', $id)->first();

        if (!$timeline) {
            return response()->json(['message' => 'Timeline not found'], 404);
        }

        return response()->json([
            'id' => $timeline->id,
            'heritageId' => $timeline->heritage_id,
            'heritageSlug' => $timeline->heritage_slug,
            'title' => $timeline->title,
            'events' => $timeline->events->map(function ($e) {
                return [
                    'year' => $e->year,
                    'title' => $e->title,
                    'description' => $e->description,
                ];
            }),
        ]);
    }
}
