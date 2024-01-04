<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RsiaSuratEksternalController extends Controller
{
    /**
     * Display a listing of the resource.
     * this function for get all data surat eksternal, data surat eksternal is include with detail pj / penanggung jawab surat eksternal. penanggung jawab surat eksternal is relation with table petugas on the database.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * 
     * @queryParam keyword string search data by keyword. Example: surat
     * @queryParam datatables boolean enable datatables. Example: true
     * 
     * authenticated
     * */ 
    public function index(Request $request)
    {
        $suratEksternal = \App\Models\RsiaSuratEksternal::with("pj_detail")->orderBy('tanggal', 'desc')->orderBy('no_surat', 'desc');

        if ($request->keyword) {
            $suratEksternal = $suratEksternal->where('no_surat', 'like', "%{$request->keyword}%")
                ->orWhere('perihal', 'like', "%{$request->keyword}%")
                ->orWhere('alamat', 'like', "%{$request->keyword}%")
                ->orWhere('pj', 'like', "%{$request->keyword}%")
                ->orWhere('tanggal', 'like', "%{$request->keyword}%")
                ->orWhereHas('pj_detail', function ($query) use ($request) {
                    $query->where('nama', 'like', "%{$request->keyword}%");
                });
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $suratEksternal->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $suratEksternal->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $suratEksternal->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, 'Data Surat Eksternal berhasil ditampilkan');
    }

    /**
     * Display the specified resource.
     * This function for get detail surat eksternal, detail surat eksternal is include with detail pj / penanggung jawab surat eksternal. penanggung jawab surat eksternal is relation with table petugas on the database.
     * 
     * @param string $id
     * @return \Illuminate\Http\Response
     * 
     * @urlParam id string required The ID of the surat eksternal. Example: 001/B/S-RSIA/101023
     * 
     * authenticated
     * */ 
    public function show($id)
    {
        if (!$id) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        $suratEksternal = \App\Models\RsiaSuratEksternal::find($id);

        if (!$suratEksternal) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        return isSuccess($suratEksternal, 'Data Surat Eksternal berhasil ditampilkan');
    }

    /**
     * Store a newly created resource in storage.
     * This function for store data surat eksternal to database, data surat eksternal is include with detail pj / penanggung jawab surat eksternal. all data is required.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * 
     * @bodyParam no_surat string required The no_surat of the surat eksternal. Example: 001/B/S-RSIA/101023
     * @bodyParam perihal string required The perihal of the surat eksternal. Example: Surat Eksternal
     * @bodyParam alamat string required The alamat of the surat eksternal. Example: Jl. Raya Bogor
     * @bodyParam pj string required The pj of the surat eksternal. Example: 1.101.1112
     * @bodyParam tanggal date required The tanggal of the surat eksternal. Example: 2021-01-01
     * 
     * authenticated
     * 
     * @response {
     *  "status": "success",
     *  "message": "Data Surat Eksternal berhasil ditambahkan",
     *  "data": {
     *      "no_surat": "001/B/S-RSIA/101023",
     *      "perihal": "Surat Eksternal",
     *      "alamat": "Jl. Raya Bogor",
     *      "pj": "1.101.1112",
     *      "tanggal": "2021-01-01",
     *  }
     * }
     * */ 
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|unique:rsia_surat_eksternal,no_surat',
            'perihal' => 'required',
            'alamat' => 'required',
            'pj' => 'required',
            'tanggal' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors());
        }

        $suratEksternal = \App\Models\RsiaSuratEksternal::create($request->except('payload'));

        return isSuccess($suratEksternal, 'Data Surat Eksternal berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     * This function for update data surat eksternal to database, data surat eksternal is include with detail pj / penanggung jawab surat eksternal. all data is required for update data, you can't update no_surat. no_surat is primary key and must be unique.
     * 
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $nomor
     * @return \Illuminate\Http\Response
     * 
     * @urlParam nomor string required The nomor of the surat eksternal. Example: 001/B/S-RSIA/101023
     * 
     * @bodyParam no_surat string required The no_surat of the surat eksternal. Example: 001/B/S-RSIA/101023
     * @bodyParam perihal string required The perihal of the surat eksternal. Example: Surat Eksternal
     * @bodyParam alamat string required The alamat of the surat eksternal. Example: Jl. Raya Bogor
     * @bodyParam pj string required The pj of the surat eksternal. Example: 1.101.1112
     * @bodyParam tanggal date required The tanggal of the surat eksternal. Example: 2021-01-01
     * 
     * authenticated 
    */
    public function update(Request $request)
    {
        if (!$request->nomor) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        $nomor = $request->nomor;
        $suratEksternal = \App\Models\RsiaSuratEksternal::find($nomor);

        if (!$suratEksternal) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'no_surat' => 'required|unique:rsia_surat_eksternal,no_surat,' . $nomor . ',no_surat',
            'perihal' => 'required',
            'alamat' => 'required',
            'pj' => 'required',
            'tanggal' => 'required',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors());
        }

        $request->merge(['no_surat' => $request->nomor]);
        $suratEksternal->update($request->except('payload', 'no_surat', 'nomor'));

        return isSuccess($suratEksternal, 'Data Surat Eksternal berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     * This function for delete data surat eksternal from database.
     * 
     * @param string $nomor
     * @return \Illuminate\Http\Response
     * 
     * @urlParam nomor string required The nomor of the surat eksternal. Example: 001/B/S-RSIA/101023
     * 
     * authenticated
     * 
     * @response {
     *  "status": "success",
     *  "message": "Data Surat Eksternal berhasil dihapus",
     * }
     * */ 
    public function destroy(Request $request)
    {
        if (!$request->nomor) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        $nomor = $request->nomor;
        $suratEksternal = \App\Models\RsiaSuratEksternal::find($nomor);

        if (!$suratEksternal) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        $suratEksternal->delete();

        return isSuccess($suratEksternal, 'Data Surat Eksternal berhasil dihapus');
    }

    /**
     * Get last nomor surat eksternal.
     * This function for get last nomor surat eksternal from database.
     * 
     * @return \Illuminate\Http\Response
     * 
     * authenticated
     * 
     * @response {
     *  "status": "success",
     *  "message": "Data Surat Eksternal berhasil ditampilkan",
     *  "data": {
     *    "no_surat": "001/B/S-RSIA/101023", 
     *  }
     * }
     * */ 
    public function getLastNomor()
    {
        $lastNomor = \App\Models\RsiaSuratEksternal::select('no_surat')
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_surat', 'desc')
            ->whereYear('tanggal', date('Y'))
            ->first();

        if (!$lastNomor) {
            return isFail('Data Surat Eksternal tidak ditemukan');
        }

        return isSuccess($lastNomor, 'Data Surat Eksternal berhasil ditampilkan');
    }
}
