@extends('template.potrait-kop')
@section('content')
<center class="mb-3">
    <u>
        <h5 style="font-family: serif; font-size: 14pt; font-weight: bold;">MEMO INTERNAL</h5>
    </u>
</center>

{{-- width 80% centered --}}

<div style="width: 95%; margin: auto;">
    <table class="table table-borderless table-sm">
        <tr>
            <td>Dari</td>
            <td>:</td>
            <td>{{ $memo->dari }}</td>
        </tr>
        <tr>
            <td>Untuk</td>
            <td>:</td>
            <td>
                {{-- loop $memo->penerima and make list ol --}}
                <ol>
                    @foreach ($memo->penerima as $penerima)
                    <li>{{ $penerima->pegawai->nama }}</li>
                    @endforeach
                </ol>
            </td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td>{{ $memo->perihal->perihal }}</td>
        </tr>
    </table>

    {{-- isi memo --}}
    <p>{!! $memo->content !!}</p>

    <div class="mt-5"></div>

    {{-- tanggal on right --}}
    <p class="p-0 m-0 text-right">Pekalongan, {{ \Carbon\Carbon::parse($memo->tanggal)->isoFormat('D MMMM Y') }}</p>

    <div class="mb-3"></div>

    {{-- mengetahui --}}
    {{-- if $count_mengetahui == 1 --}}
    @if ($count_mengetahui == 1)
    <table class="table table-borderless">
        <tr class="text-center">
            <td class="text-center">
                <p class="p-0 m-0">Mengetahui,</p>
                <p class="font-bold">{{ \App\Helpers\Pegawai::getMe($memo->mengetahui)->jbtn }}</p>
                <br><br><br><br>
                <p class="">{{ \App\Helpers\Pegawai::getMe($memo->mengetahui)->nama }}</p>
            </td>
            <td class="text-right">
                <br>
                <p class="font-bold">{{ $memo->perihal->pegawai_detail->jbtn }}</p>
                <br><br><br><br>
                <p class="">{{ $memo->perihal->pegawai_detail->nama }}</p>
            </td>
        </tr>
    </table>
    @else
    <div class="text-center">Mengetahui</div>
    <table class="table table-borderless">
        <tr>
            @foreach ($mengetahui as $m)
            <td class="text-center">
                <p class="p-0 m-0 text-center font-bold">{{ \App\Helpers\Pegawai::getMe($m)->jbtn }}</p>
                <br><br><br><br>
                <p class="text-center">{{ \App\Helpers\Pegawai::getMe($m)->nama }}</p>
            </td>
            @endforeach
        </tr>
        <tr>
            <td colspan="{{ $count_mengetahui }}" class="text-center">
                <p class="p-0 m-0 text-center">Penanggung Jawab</p>
                <p class="p-0 m-0 text-center font-bold">{{ $memo->perihal->pegawai_detail->jbtn }}</p>
                <br><br><br><br>
                <p class="text-center">{{ $memo->perihal->pegawai_detail->nama }}</p>
            </td>
        </tr>
    </table>
    @endif
</div>
@endsection