<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    public function index()
    {
        $file    = \App\Models\FileManager::get();
        return isSuccess($file);

    }

}
