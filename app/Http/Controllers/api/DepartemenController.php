<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $dept = \App\Models\Departemen::select('dep_id', 'nama');

        if ($request->q) {
            $dept = $dept->where('nama', 'like', '%' . $request->q . '%');
        }

        $dept = $dept->get();

        return isSuccess($dept, 'Data departemen berhasil ditemukan');
    }
}
