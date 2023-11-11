<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $bid = \App\Models\Jabatan::select('*');

        if ($request->q) {
            $bid = $bid->where('nm_jbtn', 'like', '%' . $request->q . '%');
        }

        $bid = $bid->get();

        return isSuccess($bid, 'Data Jabatan berhasil ditemukan');
    }
    
    public function jenjang(Request $request)
    {
        $bid = \App\Models\JenjangJabatan::select('kode', 'nama');

        if ($request->q) {
            $bid = $bid->where('nama', 'like', '%' . $request->q . '%');
        }

        $bid = $bid->get();

        return isSuccess($bid, 'Data Jenjang Jabatan berhasil ditemukan');
    }
}
