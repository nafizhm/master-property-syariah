@extends('layouts.app')
@section('content')
    <div class="container">
        @php
            $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
        @endphp
        <center><img src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" width="10%" class="mt-3"></center>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    <center>
                        <b>Terima Kasih</b>
                        <br><br>
                        Aduan atas nama:<br>
                        <b>{{ $nama }}</b><br>
                        Lokasi Perumahan: <b>{{ $lokasi }}</b><br>
                        Blok Unit Rumah: <b>{{ $blok }}</b><br>
                        Telah kami terima.
                        <br><br>
                        Nomor aduan anda <b>{{ $no_aduan }}. </b>
                        <br> Untuk pengecekan status aduan dapat dilakukan pada link berikut :
                        <br><a href="{{ route('tracking.form') }}" target="_blank">{{ route('tracking.form') }}</a>
                        <br><br>
                        <b>ADMIN GESYA GROUP</b>
                    </center>
                </div>
            </div>
        </section>
    </div>
@endsection
