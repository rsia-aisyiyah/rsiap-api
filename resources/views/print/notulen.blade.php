@extends('template.potrait-kop')
@section('content')
<style>
  /* p on konten */
  #konten p {
    margin-bottom: 0.8em;
    padding: 0;
    line-height: 1.5;
  }
</style>

<div style="font-family: Times New Roman, Times, serif; font-size: 12pt; font-weight: normal; height: 100%;">
  <div>
    <center class="mb-3">
      <u>
        <h5 style="font-family: serif; font-size: 14pt; font-weight: bold;">NOTULEN RAPAT</h5>
      </u>
    </center>

    <table class="">
      <tr>
        <th style="width: 4cm !important;">Hari / Tanggal</th>
        <td>: {{ \Carbon\Carbon::parse($notulen->surat->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
      </tr>
      <tr>
        <th style="width: 4cm !important;">Waktu</th>
        <td>: {{ \Carbon\Carbon::parse($notulen->surat->tanggal)->isoFormat('HH:mm') }} WIB s/d selesai</td>
      </tr>
      <tr>
        <th style="width: 4cm !important;">Tempat</th>
        <td>: {{ $notulen->surat->tempat }}</td>
      </tr>
      <tr>
        <th style="width: 4cm !important;">Agenda</th>
        <td>: {{ $notulen->surat->perihal }}</td>
      </tr>
      <tr>
        <th style="width: 4cm !important;">Jumlah Peserta</th>
        <td>: {{ $notulen->peserta_count }} Peserta</td>
      </tr>
    </table>

    <div class="mt-4" id="konten">
      <h5 style="font-family: serif; font-size: 12pt; font-weight: bold;">Pembahasan</h5>
      {!! $notulen->pembahasan !!}
    </div>
  </div>

  <div class="mt-5">
    <table class="table table-sm table-borderless">
      <tr>
        <td>
          <div class="text-center">
            <br>
            <p class="m-0 p-0">Pimpinan Rapat</p>
            <div class="my-5"></div>
            <p class="m-0 p-0"><u class="p-0 m-0">{{ $notulen->surat->penanggung_jawab->nama }}</u></p>
            <p class="m-0 p-0">NIP. {{ $notulen->surat->penanggung_jawab->nik }}</p>
          </div>
        </td>
        <td>
          <div class="text-center">
            <p class="p-0 m-0">Pekalongan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p class="m-0 p-0">Notulis</p>
            <div class="my-5"></div>
            <p class="m-0 p-0"><u class="p-0 m-0">{{ $notulen->notulis->nama }}</u></p>
            <p class="m-0 p-0">NIP. {{ $notulen->notulis->nik }}</p>
          </div>
        </td>
      </tr>
    </table>
  </div>
  @endsection