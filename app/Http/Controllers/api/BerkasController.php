<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BerkasController extends Controller
{
    public function index(Request $request)
    {
        $payload = auth()->payload();
        $nik = $payload->get('sub');

        $berkas = \App\Models\BerkasPegawai::with('master_berkas_pegawai')
            ->where('nik', $nik)
            ->get();

        if (!$berkas) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($berkas, "berkas pegawai berhasil diambil");
    }

    public function get_berkas(Request $request)
    {
        if (!$request->nik) {
            return isFail('nik is required', 422);
        }

        // $berkas = \App\Models\BerkasPegawai::with('master_berkas_pegawai')
        $pegawai = \App\Models\Pegawai::select('id', 'nik', 'nama', 'jbtn', 'bidang')->with(['berkas', 'berkas.master_berkas_pegawai'])
            ->where('nik', $request->nik);

        if ($request->kode) {
            $pegawai = $pegawai->whereHas('berkas', function ($query) use ($request) {
                $query->where('kode_berkas', $request->kode);
            });
            $pegawai = $pegawai->first();

            if (!$pegawai) {
                return isFail('Pegawai not found', 404);
            }
    
            return isSuccess($pegawai, "pegawai dan berkas berhasil diambil");
        }

        $pegawai = $pegawai->first();

        if (!$pegawai) {
            return isFail('Pegawai not found', 404);
        }

        return isSuccess($pegawai, "pegawai dan berkas berhasil diambil");
    }

    public function upload(Request $request)
    {
        $file = $request->file('file_berkas');
        if (!$file) {
            return isFail('file is required', 422);
        }

        if (!$request->nik) {
            return isFail('nik is required', 422);
        }

        if (!$request->berkas) {
            return isFail('kode_berkas is required', 422);
        }

        try {
            $st = new \Illuminate\Support\Facades\Storage();
            $np = 'pages/berkaspegawai/berkas/';
        
            // build file name replace all dot on nik-filename.extension
            $filename = str_replace('.', '-', $request->nik) . '-' . str_replace(' ', '-', $file->getClientOriginalName());
            $st::disk('sftp')->put(env('DOCUMENT_SAVE_LOCATION') . $filename, file_get_contents($file));
        
            // file with path pages/berkaspegawai/berkas/
            $file_path = $np . $filename;
        
            // save to database
            $berkas = new \App\Models\BerkasPegawai();
            $berkas->nik = $request->nik;
            $berkas->tgl_uploud = date('Y-m-d');
            $berkas->kode_berkas = $request->berkas;
            $berkas->berkas = $file_path;
            $berkas->save();
        
            return isSuccess($berkas, "berkas pegawai berhasil diupload");
        } catch (\Exception $e) {
            // Handle the exception
            return isFail("Gagal mengupload berkas pegawai: " . $e->getMessage(), 500);
        }        
    }

    public function delete(Request $request)
    {
        // in request nik, kode, berkas (path of saved berkas)
        if (!$request->nik) {
            return isFail('nik is required', 422);
        }

        if (!$request->kode) {
            return isFail('kode is required', 422);
        }

        if (!$request->berkas) {
            return isFail('berkas is required', 422);
        }

        try {
            $st = new \Illuminate\Support\Facades\Storage();

            $berkas = \App\Models\BerkasPegawai::where('nik', $request->nik)
                ->where('kode_berkas', $request->kode)
                ->where('berkas', $request->berkas)
                ->delete();
            
            // if berkas exist on storage delete it 
            if ($request->berkas && $st::disk('sftp')->exists('webapps/penggajian/' . $request->berkas)) {
                $st::disk('sftp')->delete('webapps/penggajian/' . $request->berkas);
            }

            return isSuccess([
                "nik" => $request->nik,
            ], "berkas pegawai berhasil dihapus");
        } catch (\Exception $e) {
            // Handle the exception
            return isFail("Gagal menghapus berkas pegawai: " . $e->getMessage(), 500);
        }
    }

    public function get_kategori(Request $request)
    {
        $kategori = \App\Models\MasterBerkasPegawai::select('kategori', 'kode')->groupBy('kategori');

        if ($request->spk_rkk && $request->spk_rkk == 'true' || $request->spk_rkk == 1) {
            $kategori = $kategori->whereIn('kode', ['MBP0006', 'MBP0019', 'MBP0032', 'MBP0045']);
        }

        $kategori = $kategori->get();

        return isSuccess($kategori, "kategori berhasil diambil");
    }

    public function get_nama_berkas(Request $request)
    {
        if (!$request->kategori) {
            return isFail('kategori is required', 422);
        }

        $nama_berkas = \App\Models\MasterBerkasPegawai::select('nama_berkas as nama', 'kode')
            ->where('kategori', $request->kategori)
            ->get();

        return isSuccess($nama_berkas, "nama berkas berhasil diambil");
    }
}
