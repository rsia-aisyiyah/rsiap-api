<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function index(Request $request)
    {
        $pendidikan = \App\Models\Pendidikan::select('tingkat');

        if($request->q) {
            $pendidikan->where('tingkat', 'LIKE', "%$request->q%");
        }

        $pendidikan = $pendidikan->orderBy('tingkat', 'ASC')->get();

        return isSuccess($pendidikan, "Berhasil mengambil data pendidikan");
    }
}
