<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryApiController extends Controller
{
    public function index()
    {
        $categories = Category::all()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'icon' => $c->icon ?? 'account_balance',
                'description' => $c->description ?? '',
            ];
        });

        return response()->json($categories);
    }
}
