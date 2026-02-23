<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;

class AssistantController extends Controller
{
    public function index()
    {
        return view('assistant.index');
    }
}
