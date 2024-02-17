@extends('template.potrait-kop')
@section('content')
<div class="mb-3 text-center">
  <img src="{{ asset('public/images/bismillah-2.png') }}" alt="basmallah" style="width: 35%">
</div>

<div class="text-right mb-3">
  Pekalongan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
</div>

<table class="mb-3">
  <tr>
    <th style="width: 300px;">Nomor</th>
    <td style="width: 50px;">:</td>
    <td>
      <?= $nomor ?>
    </td>
  </tr>
  <tr>
    <th style="width: 300px;">Hal</th>
    <td style="width: 50px;">:</td>
    <td>Undangan</td>
  </tr>
  <tr>
    <th style="width: 300px;">Lampiran</th>
    <td style="width: 50px;">:</td>
    <td>-</td>
  </tr>
</table>

<div class="mb-2">
  <span class="p-0 m-0">Kepada Yth.</span>
  <table class="table table-borderless table-sm mb-0">
    <tr>
      @foreach ($penerima as $key => $val)
      <td class="p-0 m-0" style="width: 50px;">
        <ol class="mb-0 pb-0">
          @foreach ($val as $v)
            <li>{{ $v->pegawai->nama }}</li>
          @endforeach
        </ol>
      </td>
      @endforeach
    </tr>
  </table>
</div>

<p class="mb-3">
  Di <br />
  <span class="ml-5">Tempat</span>
</p>

<p style="line-height: 1.8">
  <strong><i>Assalamu'alaikum Warahmatullahi Wabarakatuh</i></strong> <br />
  Puji syukur kami panjatkan kepada Allah SWT atas rahmat-Nya yang melimpah. Kami bersyukur atas petunjuk-Nya yang tak
  pernah terputus. Semoga kita selalu berada dalam lindungan-Nya dan mendapatkan keberkahan-Nya. Aamiin. <br />
  Dihomon kehadirannya pada :
</p>

<div style="width: 90%; margin: auto;" class="mb-0">
  <table class="table table-borderless table-sm">
    <tr>
      <th style="width: 20%;">Hari</th>
      <td style="width: 20px;">:</td>
      <td>{{ \Carbon\Carbon::parse($undangan->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Jam</th>
      <td style="width: 20px;">:</td>
      <td>{{ \Carbon\Carbon::parse($undangan->tanggal)->isoFormat('HH:mm') }} WIB s/d selesai</td>
    </tr>
    <tr>
      <th style="width: 20%;">Tempat</th>
      <td style="width: 20px;">:</td>
      <td>{{ $undangan->tempat }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Acara</th>
      <td style="width: 20px;">:</td>
      <td>{{ $undangan->perihal }}</td>
    </tr>
  </table>
</div>

<p class="mb-5">
  Demikian disampaikan,. Terimakasih. <br />
  <i>Nasrun minallohi wa fatkhun qorieb.</i> <br />
  <strong><i>Wassalamu'alaikum Warahmatullahi Wabarakatuh</i></strong>
</p>

<table class="table table-borderless table-sm">
  <tr>
    <td class="p-0 m-0" style="width:27%;"></td>
    <td class="p-0 m-0" style="width:27%;"></td>
    <td class="p-0 m-0">
      <p class="mb-0 pb-0">Pekalongan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
      {{-- $undangan->pegawai_detail->jenjang_jabatan->nama is capital make is cammel case--}}
      <p class="mb-0 pb-0" style="font-weight: bold; text-transform: capitalize;">{{ strtolower($undangan->pegawai_detail->jenjang_jabatan->nama) }}</p>
      <br /><br /><br />
      <p class="mb-0 pb-0" style="font-weight: bold">{{ $undangan->pegawai_detail->nama }}</p>
    </td>
  </tr>
</table>
@endsection