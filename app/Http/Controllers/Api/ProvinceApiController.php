<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;

class ProvinceApiController extends Controller
{
    public function index()
    {
        $provinces = Province::all()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'island' => $p->island ?? '',
            ];
        });

        return response()->json($provinces);
    }
}
