<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RsiaAgendaController extends Controller
{
    public function index(Request $request)
    {
        $agenda = \App\Models\RsiaAgenda::select("*")->orderBy('id', 'DESC')->where('show', '1');

        if ($request->keyword) {
            $agenda = $agenda->where('judul', 'like', "%{$request->keyword}%")
                ->orWhere('keterangan', 'like', "%{$request->keyword}%")
                ->orWhere('tempat', 'like', "%{$request->keyword}%")
                ->orWhere('pj', 'like', "%{$request->keyword}%")
                ->whereHas('pegawai', function ($query) use ($request) {
                    $query->where('nama', 'like', "%{$request->keyword}%");
                });
        }

        // status
        if ($request->status) {
            $agenda = $agenda->where('status', $request->status);
        }

        // tanggal
        if ($request->tanggal) {
            list($start, $end) = explode(',', $request->tanggal);
            $agenda = $agenda->whereDateBetween('tanggal', [$start, $end]);
        }

        if ($request->datatables) {
            if ($request->datatables == 1 || $request->datatables == true || $request->datatables == 'true') {
                $data = $agenda->get();
                return \Yajra\DataTables\DataTables::of($data)->make(true);
            } else {
                $data = $agenda->paginate(env('PER_PAGE', 10));
            }
        } else {
            $data = $agenda->paginate(env('PER_PAGE', 10));
        }

        return isSuccess($data, 'Data berhasil ditemukan');
    }

    // get calendar
    public function calendar(Request $request)
    {
        $agenda = \App\Models\RsiaAgenda::select("*")->orderBy('id', 'DESC')->where('show', '1');
        $rsia_surat_internal = \App\Models\RsiaSuratInternal::select('no_surat', 'tempat', 'pj', 'perihal as title', 'tanggal as date', 'tanggal', 'status')
            ->whereHas('penerima')
            ->with(['pj_detail' => function ($query) {
                $query->select('nip', 'nama');
            }]);

        $start = $request->start ? $request->start : date('Y-m-01');
        $end = $request->end ? $request->end : date('Y-m-t');

        $agenda = $agenda->whereBetween('tanggal', [$start, $end])->get();
        $rsia_surat_internal = $rsia_surat_internal->whereBetween('tanggal', [date('Y-m-d', strtotime($start . ' +1 day')), $end])->get();

        // map agenda 
        $allKegiatan = [];
        foreach ($agenda as $key => $value) {
            $agendaMap[] = [
                'title' => $value->agenda,
                'start' => $value->tanggal . ' ' . $value->start,
                'end' => $value->tanggal . ' ' . ($value->end ? $value->end : $value->start),
                'resource' => [
                    'hasSurat' => false,
                    'id' => $value->id,
                    'tempat' => $value->tempat,
                    'tanggal' => $value->tanggal,
                    'status' => $value->status,
                    'pj' => $value->pj,
                    'pj_detail' => $value->petugas,
                    'keterangan' => $value->keterangan,
                    'start' => $value->start,
                    'end' => $value->end,
                ]
            ];
        }

        // map surat internal
        foreach ($rsia_surat_internal as $key => $value) {
            $agendaMap[] = [
                'title' => $value->title,
                'start' => $value->tanggal,
                'end' => $value->tanggal,
                'resource' => [
                    'hasSurat' => true,
                    'tempat' => $value->tempat,
                    'tanggal' => $value->tanggal,
                    'status' => $value->status,
                    'no_surat' => $value->no_surat,
                    'pj' => $value->pj,
                    'pj_detail' => $value->pj_detail,
                ]
            ];
        }

        $data = [
            'start' => $start,
            'end' => $end,
            'agenda' => $agendaMap
        ];

        return isSuccess($data, 'Data berhasil ditemukan');
    }

    // get
    public function show($id)
    {
        $agenda = \App\Models\RsiaAgenda::with('pegawai')->find($id);

        if (!$agenda) {
            return isFail('Data tidak ditemukan', 404);
        }

        return isSuccess($agenda, 'Data berhasil ditemukan');
    }

    // post store
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'agenda' => 'required',
            'pj' => 'required',
            'tempat' => 'required',
            'tanggal' => 'required|date_format:Y-m-d',
            'start' => 'required|date_format:H:i',
            
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // save all on request except payload
        $agenda = \App\Models\RsiaAgenda::create($request->except('payload'));
        
        if (!$agenda) {
            return isFail('Data gagal disimpan', 500);
        }

        return isSuccess($agenda, 'Data berhasil disimpan');
    }

    // put update by id
    public function update(Request $request, $id)
    {
        $agenda = \App\Models\RsiaAgenda::find($id);

        if (!$agenda) {
            return isFail('Data tidak ditemukan', 404);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'agenda' => 'required',
            'pj' => 'required',
            'tempat' => 'required',
            'tanggal' => 'required|date_format:Y-m-d',
            'start' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        // save all on request except payload
        $agenda = $agenda->update($request->except('payload'));

        if (!$agenda) {
            return isFail('Data gagal disimpan', 500);
        }

        return isSuccess($agenda, 'Data berhasil disimpan');
    }

    public function updateStatus($id)
    {

        if (!$id) {
            return isFail("Data tidak ditemukan", 404);
        } 

        // validate request
        $request = \Illuminate\Support\Facades\Request::all();
        $validator = \Illuminate\Support\Facades\Validator::make($request, [
            'status' => 'required|in:pengajuan,ditolak,disetujui',
        ]);

        if ($validator->fails()) {
            return isFail($validator->errors(), 422);
        }

        $agenda = \App\Models\RsiaAgenda::find($id);

        if (!$agenda) {
            return isFail("Data tidak ditemukan $id", 404);
        }

        $agenda = $agenda->update(['status' => $request['status']]);

        if (!$agenda) {
            return isFail('Data gagal disimpan', 500);
        }

        return isSuccess($agenda, 'Data berhasil disimpan');
    }

    // delete by id make show to 0
    public function delete($id)
    {
        $agenda = \App\Models\RsiaAgenda::find($id);

        if (!$agenda) {
            return isFail('Data tidak ditemukan', 404);
        }

        $agenda = $agenda->update(['show' => '0']);

        if (!$agenda) {
            return isFail('Data gagal dihapus', 500);
        }

        return isSuccess($agenda, 'Data berhasil dihapus');
    }

    // delete by id
    public function destroy($id)
    {
        $agenda = \App\Models\RsiaAgenda::find($id);

        if (!$agenda) {
            return isFail('Data tidak ditemukan', 404);
        }

        $agenda = $agenda->delete();

        if (!$agenda) {
            return isFail('Data gagal dihapus', 500);
        }

        return isSuccess($agenda, 'Data berhasil dihapus');
    }
}
