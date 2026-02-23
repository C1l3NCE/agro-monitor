<?php

namespace App\Http\Controllers;

use App\Models\Field;

class MapController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $fields = $user->isAgronom()
            ? Field::where('user_id', $user->id)->get()
            : Field::all();

        return view('map.index', compact('fields'));
    }

    public function embed()
    {
        $user = auth()->user();

        $fields = $user->isAgronom()
            ? Field::where('user_id', $user->id)->get()
            : Field::all();

        return view('map.embed', compact('fields'));
    }
}
