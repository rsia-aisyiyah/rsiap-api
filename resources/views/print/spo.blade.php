@extends('template.spo')
@section('content')
<div class="p-0" style="counter-reset: pages !important;" id="spoRender">
  <table class="table table-bordered">
    <thead>
      <tr class="text-center">
        <th class="va-middle" style="width: 23% !important;" rowspan="2">
          <div class="flex flex-col items-center align-middle" style="padding: 7px 0px">
            <h6><strong>RSIA AISYIYAH PEKAJANGAN</strong></h6>
            <img src="{{ asset('public/images/logo.png') }}" alt="logo" class="img-fluid" width="65%">
          </div>
        </th>
        <th class="va-middle" colspan="2">
          <h5><strong>{{$spo->judul}}</strong></h5>
        </th>
      </tr>

      <tr class="text-center">
        <th class="va-middle">
          <div class="flex flex-col">
            <p class="p-0 m-0"><small>Nomor Surat : </small></p>
            <strong style="white-space: nowrap">
              {{ $spo->nomor }}
            </strong>
          </div>
        </th>
        <th class="va-middle">
          <p class="p-0 m-0"><small>Halaman : </small></p>
          <span class="pagenum" id="pagenum" style="color: transparent !important;">1/1</span>
        </th>
      </tr>
    </thead>

    <tbody>
      <tr class="text-center">
        <td class="va-middle">
          <strong>
            STANDAR PROSEDUR OPERASIONAL
          </strong>
        </td>
        <td class="va-middle">
          <div class="flex flex-col">
            <p class="p-0 m-0"><small>Tanggal Terbit : </small></p>
            <strong style="white-space: nowrap">
              {{ date('d F Y', strtotime($spo->tgl_terbit)) }}
            </strong>
          </div>
        </td>
        <td class="va-middle" style="position: relative;">
          <div class="flex flex-col" style="position: relative;">
            <p class="p-0 m-0"><small>Ditetapkan </small></p>
            <p class="p-0 m-0"><small>Direktur RSIA Aisyiyah Pekajangan </small></p>
            @if ($spo->is_verified)
              <img src="{{ asset('public/images/ttd-dr-him.jpeg') }}" alt="logo" class="img-fluid pt-2" width="40%">
            @else
              <div class="my-5"></div>
            @endif
            <strong
              style="white-space: nowrap; position: relative; margin: 0 !important; padding: 0 !important; top: -60px !important; line-height: 0 !important">dr.
              Himawan Budityastomo, Sp.OG</strong>
          </div>
        </td>
      </tr>

      {{-- loop pengertian, tujuan, kebijakan and prosedur and get content from $spo->detail['pengertian ...'] --}}
      @foreach ($detail as $key => $value)
      <tr>
        <td class="text-center">
          <p class="p-0 m-0"><strong>{{ ucfirst($key) }}</strong></p>
        </td>
        <td colspan="2" style="text-align: justify;">
          <p class="p-0 m-0">{!! $value !!}</p>
        </td>
      </tr>
      @endforeach

      {{-- unit | $spo->unit content is FARMASI, UGD | explode by , and make list of them --}}
      <tr>
        <td class="text-center">
          <p class="p-0 m-0"><strong>Unit Terkait</strong></p>
        </td>
        <td colspan="2">
          <ol class="list-unstyled">
            @foreach (explode(',', $spo->unit_terkait) as $unit)
            <li>{{ $unit }}</li>
            @endforeach
          </ol>
        </td>

    </tbody>
  </table>
</div>

<script type="text/php">
  if (isset($pdf)) { 
      //Shows number center-bottom of A4 page with $x,$y values
      $x = 433;  //X-axis i.e. vertical position 
      $y = 143; //Y-axis horizontal position
      
      $text = "{PAGE_NUM} / {PAGE_COUNT}";  //format of display message
      $font =  $fontMetrics->get_font("helvetica", "bold");
      $size = 12;
      $color = array(0,0,0);

      $word_space = 0.0;  //  default
      $char_space = 0.0;  //  default
      $angle = 0.0;   //  default
      
      $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
  }  
</script>
@stop