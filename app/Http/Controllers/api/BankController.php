<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $bank = \App\Models\Bank::select('*');

        if ($request->q) {
            $bank = $bank->where('namabank', 'like', '%' . $request->q . '%');
        }

        $bank = $bank->get();

        return isSuccess($bank, 'Data bank berhasil ditemukan');
    }
}
