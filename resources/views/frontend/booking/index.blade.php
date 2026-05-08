@extends('layouts.app')

@section('title', 'Booking Sentosa Era Wijaya')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
            background:
                linear-gradient(rgba(8, 37, 19, 0.72), rgba(8, 37, 19, 0.72)),
                url('{{ asset('config_media/booking-bg.png') }}') no-repeat center center fixed !important;
            background-size: cover !important;
            min-height: 100vh;
        }

        .booking-page {
            padding: 32px 16px 48px;
        }

        .booking-container {
            max-width: 860px;
            margin: 0 auto;
        }

        .booking-logo {
            width: 132px;
            max-width: 100%;
            height: auto;
            margin: 0 auto 20px;
            display: block;
            filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.25));
        }

        .booking-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.24);
            backdrop-filter: blur(10px);
        }

        .booking-header {
            padding: 28px 32px;
            background: linear-gradient(135deg, #0d5c2e 0%, #1a7a42 50%, #228b4a 100%);
            text-align: center;
        }

        .booking-header h4 {
            color: #fff;
            font-weight: 800;
            margin: 0;
            font-size: 1.3rem;
            letter-spacing: 0.02em;
        }

        .booking-header p {
            color: rgba(255, 255, 255, 0.82);
            margin: 8px 0 0;
            font-size: 0.92rem;
        }

        .booking-body {
            padding: 32px;
        }

        .stage-section {
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInUp 0.45s ease forwards;
        }

        .stage-section:nth-child(1) {
            animation-delay: 0.08s;
        }

        .stage-section:nth-child(2) {
            animation-delay: 0.16s;
        }

        .stage-section:nth-child(3) {
            animation-delay: 0.24s;
        }

        .stage-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e8f5e9;
        }

        .stage-number {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a7a42, #2ea55a);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .stage-title {
            margin: 0;
            color: #1a5c30;
            font-size: 1rem;
            font-weight: 700;
        }

        .section-card {
            background: linear-gradient(180deg, #fcfefd 0%, #f7fbf8 100%);
            border: 1px solid #e1efe4;
            border-radius: 16px;
            padding: 22px 22px 10px;
        }

        .section-card+.section-card {
            margin-top: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #374151;
        }

        .col-form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #374151;
            padding-top: 0;
        }

        .required-star {
            color: #ef4444;
            font-weight: 700;
        }

        .field-shell {
            position: relative;
        }

        .form-control,
        .input-group-text {
            border-radius: 10px !important;
            font-size: 16px;
        }

        .form-control {
            border: 1.5px solid #d1d5db !important;
            min-height: 42px;
            padding: 9px 13px;
            transition: all 0.2s ease;
        }

        textarea.form-control {
            min-height: auto;
        }

        .form-control:focus {
            border-color: #2ea55a !important;
            box-shadow: 0 0 0 3px rgba(46, 165, 90, 0.14) !important;
        }

        .form-control[readonly] {
            background: #f0fdf4;
            color: #1a5c30;
            font-weight: 600;
        }

        .input-group-text {
            border: 1.5px solid #d1d5db !important;
            background: #f8fafc;
            color: #1a5c30;
            font-weight: 700;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--bootstrap4 .select2-selection--single {
            border-radius: 10px !important;
            border: 1.5px solid #d1d5db !important;
            height: 42px !important;
            padding: 0 12px !important;
            display: flex !important;
            align-items: center !important;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            color: #374151;
            line-height: 40px !important;
            padding-left: 0 !important;
            padding-right: 24px !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            top: 0 !important;
        }

        .select2-container--bootstrap4.select2-container--focus .select2-selection--single {
            border-color: #2ea55a !important;
            box-shadow: 0 0 0 3px rgba(46, 165, 90, 0.14) !important;
        }

        .helper-box {
            border-radius: 12px;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 1px solid #bbf7d0;
            color: #1a5c30;
            padding: 14px 16px;
            font-size: 0.9rem;
            margin-bottom: 18px;
        }

        .helper-box strong {
            color: #0f5132;
        }

        .file-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .upload-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1a7a42;
            border-radius: 14px;
            padding: 16px;
            min-height: 100%;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }

        .upload-card h6 {
            margin: 0 0 6px;
            color: #1a5c30;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .upload-card p {
            margin: 0 0 12px;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .upload-card input[type="file"] {
            display: block;
            width: 100%;
            font-size: 0.84rem;
            color: #374151;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .upload-card input[type="file"]::-webkit-file-upload-button {
            border: none;
            border-radius: 8px;
            background: #1a7a42;
            color: #fff;
            padding: 8px 12px;
            margin-right: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        .preview-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .preview-actions .btn {
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 6px 12px;
        }

        .btn-submit {
            min-width: 220px;
            border: none;
            border-radius: 12px;
            background: #1a7a42;
            color: #fff;
            padding: 14px 36px;
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.25s ease;
            box-shadow: 0 12px 24px rgba(26, 122, 66, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: #136f39;
            color: #fff;
        }

        .btn-submit:disabled {
            opacity: 0.75;
            transform: none;
        }

        .is-invalid,
        .select2-invalid .select2-selection {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 16px;
            overflow: hidden;
            border: none;
        }

        .modal-header.bg-indigo {
            background: linear-gradient(135deg, #0d5c2e 0%, #1a7a42 100%) !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media screen and (max-width: 768px) {

            input,
            select,
            textarea {
                font-size: 16px !important;
            }
        }

        @media (max-width: 767.98px) {
            .booking-page {
                padding: 22px 12px 40px;
            }

            .booking-header,
            .booking-body {
                padding: 22px 18px;
            }

            .section-card {
                padding: 18px 16px 6px;
            }

            .file-grid {
                grid-template-columns: 1fr;
            }

            .btn-submit {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="booking-page">
        <div class="booking-container">
            @php
                $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
            @endphp

            <img class="booking-logo" src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" alt="Logo">

            <section class="content">
                <div class="booking-card">
                    <div class="booking-header">
                        <h4>Form Data Customer (Booking)</h4>
                        <p>Silakan lengkapi data dengan benar untuk proses booking unit</p>
                        <div class="mt-4 text-center">
                            <a href="{{ route('public.siteplan.index') }}" target="_blank"
                                class="badge text-white px-3 py-2 shadow-sm"
                                style="border-radius: 50rem; font-weight: 600; font-size: 0.75rem; transition: all 0.3s; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3);">
                                <i class="fas fa-map mr-1"></i> Lihat Siteplan
                            </a>
                        </div>
                    </div>

                    <div class="booking-body">
                        <div class="helper-box">
                            <strong>Tanggal pengisian:</strong> {{ $tgl ?? now()->translatedFormat('j F Y') }}.
                            Pastikan data identitas, lokasi unit, dan dokumen pendukung diisi dengan lengkap.
                        </div>

                        <form id="formData" enctype="multipart/form-data">
                            @csrf

                            <div class="stage-section">
                                <div class="stage-header">
                                    <div class="stage-number">1</div>
                                    <h5 class="stage-title">Data Pribadi</h5>
                                </div>

                                <div class="section-card">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Tanggal</label>
                                                <div class="field-shell">
                                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                                        value="{{ date('Y-m-d') }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Nama Lengkap <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <input name="nama_lengkap" id="nama_lengkap" class="form-control"
                                                        type="text" placeholder="Masukkan nama lengkap sesuai KTP">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>NIK <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <input name="nik" id="nik" class="form-control" type="text"
                                                        placeholder="16 digit nomor KTP">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>No. Telp / WA <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <input name="no_telp" id="no_telp" class="form-control"
                                                        type="text" placeholder="08xxxxxxxxxx">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Tempat Lahir <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <input name="tempat_lahir" id="tempat_lahir" class="form-control"
                                                        type="text" placeholder="Kota kelahiran">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Tanggal Lahir <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <input name="tgl_lahir" id="tgl_lahir" class="form-control"
                                                        type="date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Jenis Kelamin <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select class="form-control select-jk" name="jenis_kelamin"
                                                        id="jenis_kelamin">
                                                        <option value=""></option>
                                                        <option value="Laki-laki">Laki-laki</option>
                                                        <option value="Perempuan">Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Status Pernikahan</label>
                                                <div class="field-shell">
                                                    <select class="form-control select-status" name="status_pernikahan"
                                                        id="status_pernikahan">
                                                        <option value=""></option>
                                                        <option value="Belum Menikah">Belum Menikah</option>
                                                        <option value="Menikah">Menikah</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <div class="field-shell">
                                                    <input name="email" id="email" class="form-control"
                                                        type="text" placeholder="nama@email.com">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>NPWP</label>
                                                <div class="field-shell">
                                                    <input name="npwp" id="npwp" class="form-control"
                                                        type="text" placeholder="Nomor NPWP">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Pekerjaan</label>
                                                <div class="field-shell">
                                                    <input name="pekerjaan" id="pekerjaan" class="form-control"
                                                        type="text" placeholder="Jenis pekerjaan">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>No. BPJS Kes</label>
                                                <div class="field-shell">
                                                    <input name="no_bpjs_kes" id="no_bpjs_kes" class="form-control"
                                                        type="text" placeholder="Nomor BPJS">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Alamat KTP <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <textarea name="alamat_ktp" id="alamat_ktp" class="form-control" rows="2" placeholder="Alamat sesuai KTP"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Alamat Domisili <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2"
                                                        placeholder="Alamat domisili saat ini"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-card" id="pasangan">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Nama Pasangan</label>
                                                <div class="field-shell">
                                                    <input name="nama_p" id="nama_p" class="form-control"
                                                        type="text" placeholder="Nama pasangan">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>NIK Pasangan</label>
                                                <div class="field-shell">
                                                    <input name="nik_p" id="nik_p" class="form-control"
                                                        type="text" placeholder="NIK pasangan">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Nama Saudara</label>
                                                <div class="field-shell">
                                                    <input name="nama_saudara" id="nama_saudara" class="form-control"
                                                        type="text" placeholder="Kontak darurat keluarga">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>No. Telp Saudara</label>
                                                <div class="field-shell">
                                                    <input name="no_telp_saudara" id="no_telp_saudara"
                                                        class="form-control" type="text" placeholder="08xxxxxxxxxx">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stage-section">
                                <div class="stage-header">
                                    <div class="stage-number">2</div>
                                    <h5 class="stage-title">Data Unit dan Transaksi</h5>
                                </div>

                                <div class="section-card">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Lokasi Perumahan <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select class="form-control select-lokasi" name="id_lokasi"
                                                        id="id_lokasi">
                                                        <option value=""></option>
                                                        @foreach ($lokasi as $l)
                                                            <option value="{{ $l->id }}">{{ $l->nama_kavling }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Blok / Kav <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select name="id_kavling" id="id_kavling"
                                                        class="form-control select-kavling"></select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Harga Rumah</label>
                                                <div class="field-shell">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" name="hrg_jual" id="hrg_jual"
                                                            class="form-control format-number" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Jenis Properti <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select class="form-control select-jenis-properti"
                                                        name="jenis_properti" id="jenis_properti">
                                                        <option value=""></option>
                                                        <option value="Ruko">Ruko</option>
                                                        <option value="Kavling">Kavling</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-card">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Marketing Inhouse <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select class="form-control select-marketing" name="id_marketing"
                                                        id="id_marketing">
                                                        <option value=""></option>
                                                        <option value="0">Non Marketing</option>
                                                        @foreach ($marketing as $m)
                                                            <option value="{{ $m->id }}">
                                                                {{ $m->nama_marketing }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Marketing Agent</label>
                                                <div class="field-shell">
                                                    <select class="form-control select-agent" name="id_agent"
                                                        id="id_agent">
                                                        <option value=""></option>
                                                        @foreach ($agent as $f)
                                                            <option value="{{ $f->id }}">{{ $f->nama_agent }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Jenis Perumahan <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select class="form-control select-jp" name="jenis_perumahan"
                                                        id="jenis_perumahan">
                                                        <option value=""></option>
                                                        <option value="Subsidi">Subsidi</option>
                                                        <option value="Komersil">Komersil</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Jenis Pembelian <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <select class="form-control select-pembelian"
                                                        name="jenis_pembelian" id="jenis_pembelian">
                                                        <option value=""></option>
                                                        <option value="Pembelian Cash">Pembelian Cash</option>
                                                        <option value="Cash Bertahap">Cash Bertahap</option>
                                                        <option value="KPR">KPR</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Booking Fee <span class="required-star">*</span></label>
                                                <div class="field-shell">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input name="booking_fee" id="booking_fee"
                                                            class="form-control format-number" type="text"
                                                            placeholder="Masukkan nominal booking fee">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stage-section">
                                <div class="stage-header">
                                    <div class="stage-number">3</div>
                                    <h5 class="stage-title">Dokumen Pendukung</h5>
                                </div>

                                <div class="helper-box">
                                    Unggah file berformat <strong>JPG, JPEG, PNG, atau PDF</strong>. File yang sudah dipilih
                                    bisa Anda preview sebelum dikirim.
                                </div>

                                <div class="file-grid">
                                    <div class="upload-card field-shell">
                                        <h6>Foto Pemohon</h6>
                                        <p>Dokumen opsional untuk melengkapi identitas pemohon.</p>
                                        <input name="foto_pemohon" id="foto_pemohon" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_foto_pemohon')">
                                        <div id="preview_foto_pemohon" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('foto_pemohon', 'preview_foto_pemohon')">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="upload-card field-shell">
                                        <h6>Foto KTP <span class="required-star">*</span></h6>
                                        <p>Dokumen utama identitas pemohon.</p>
                                        <input name="foto_ktp" id="foto_ktp" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_foto_ktp')">
                                        <div id="preview_foto_ktp" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('foto_ktp', 'preview_foto_ktp')">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="upload-card field-shell">
                                        <h6>Foto NPWP</h6>
                                        <p>Diisi bila pemohon memiliki NPWP.</p>
                                        <input name="foto_npwp" id="foto_npwp" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_foto_npwp')">
                                        <div id="preview_foto_npwp" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('foto_npwp', 'preview_foto_npwp')">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="upload-card field-shell">
                                        <h6>Foto KK</h6>
                                        <p>Dokumen kartu keluarga bila tersedia.</p>
                                        <input name="foto_kk" id="foto_kk" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_foto_kk')">
                                        <div id="preview_foto_kk" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('foto_kk', 'preview_foto_kk')">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="upload-card field-shell">
                                        <h6>Foto BPJS</h6>
                                        <p>Dokumen BPJS kesehatan bila ada.</p>
                                        <input name="foto_bpjs" id="foto_bpjs" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_foto_bpjs')">
                                        <div id="preview_foto_bpjs" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('foto_bpjs', 'preview_foto_bpjs')">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="upload-card field-shell">
                                        <h6>Foto KTP Pasangan</h6>
                                        <p>Diunggah bila status pernikahan menikah.</p>
                                        <input name="foto_ktp_p" id="foto_ktp_p" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_foto_ktp_p')">
                                        <div id="preview_foto_ktp_p" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('foto_ktp_p', 'preview_foto_ktp_p')">Hapus</button>
                                        </div>
                                    </div>

                                    <div class="upload-card field-shell" style="grid-column: 1 / -1;">
                                        <h6>Bukti Transfer</h6>
                                        <p>Unggah bukti pembayaran booking fee jika sudah tersedia.</p>
                                        <input name="file_bukti" id="file_bukti" type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="handleFileChange(this, 'preview_file_bukti')">
                                        <div id="preview_file_bukti" class="preview-actions mt-3 d-none">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="showPreview(this)">View</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="clearFile('file_bukti', 'preview_file_bukti')">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center pt-2">
                                <button type="submit" class="btn btn-submit" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="button-text">Kirim Data</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="previewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title" id="previewModalLabel">Preview File</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <img id="modalPreviewImage" class="img-fluid d-none" alt="Preview">
                    <iframe id="modalPreviewPdf" class="w-100 d-none" style="height:500px;" frameborder="0"></iframe>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function formatAngkaRibuan(angka) {
            return angka.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).on('input', '#booking_fee', function() {
            $(this).val(formatAngkaRibuan($(this).val()));
        });

        function handleFileChange(input, previewId) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function(e) {
                const previewDiv = document.getElementById(previewId);
                const viewBtn = previewDiv.querySelector('button.btn-primary');

                viewBtn.setAttribute('data-src', e.target.result);
                viewBtn.setAttribute('data-type', file.type);

                input.style.display = 'none';
                previewDiv.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        }

        function showPreview(btn) {
            const src = btn.getAttribute('data-src');
            const type = btn.getAttribute('data-type');

            const img = document.getElementById('modalPreviewImage');
            const pdf = document.getElementById('modalPreviewPdf');

            if (type === 'application/pdf') {
                img.classList.add('d-none');
                pdf.classList.remove('d-none');
                pdf.src = src;
            } else {
                pdf.classList.add('d-none');
                img.classList.remove('d-none');
                img.src = src;
            }

            const myModal = new bootstrap.Modal(document.getElementById('previewModal'));
            myModal.show();
        }

        function clearFile(inputId, previewId) {
            const input = document.getElementById(inputId);
            input.value = '';
            input.style.display = 'block';

            document.getElementById(previewId).classList.add('d-none');
            document.getElementById('modalPreviewImage').src = '';
            document.getElementById('modalPreviewPdf').src = '';
        }

        function clearValidationUI() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.select2-invalid').removeClass('select2-invalid');
        }

        function appendFieldError(input, message) {
            const shell = input.closest('.field-shell').length ? input.closest('.field-shell') : input.parent();

            if (input.hasClass('select2-hidden-accessible')) {
                input.next('.select2-container').addClass('select2-invalid');
            } else {
                input.addClass('is-invalid');
            }

            shell.find('.invalid-feedback').remove();
            shell.append('<span class="invalid-feedback" role="alert"><strong>' + message + '</strong></span>');
        }

        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Lokasi',
            });

            $('.select-jk').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Jenis Kelamin',
            });

            $('.select-kavling').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Kavling',
            });

            $('.select-marketing').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Marketing',
            });

            $('.select-agent').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Agent',
            });

            $('.select-jp').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Jenis Perumahan',
            });

            $('.select-pembelian').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Jenis Pembelian',
            });

            $('.select-status').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Status',
            });

            $('.select-jenis-properti').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Jenis Properti',
            });

            const routeGetKavling = "{{ route('booking.getKavling', ':id') }}";
            const routeGetHarga = "{{ route('booking.getHargaKavling', ':id') }}";

            $('#id_lokasi').on('change', function() {
                let idLokasi = $(this).val();
                $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');
                $('#hrg_jual').val('');

                if (idLokasi) {
                    const urlKavling = routeGetKavling.replace(':id', idLokasi);
                    $.get(urlKavling, function(data) {
                        let options = '<option value=""></option>';
                        data.forEach(function(item) {
                            options +=
                                `<option value="${item.id}">${item.kode_kavling}</option>`;
                        });
                        $('#id_kavling').html(options).trigger('change');
                    });
                } else {
                    $('#id_kavling').html('<option value=""></option>').trigger('change');
                }
            });

            $('#id_kavling').on('change', function() {
                let idKavling = $(this).val();

                if (idKavling) {
                    const urlHarga = routeGetHarga.replace(':id', idKavling);
                    $.get(urlHarga, function(data) {
                        $('#hrg_jual').val(formatRupiah(data.hrg_jual));
                    });
                } else {
                    $('#hrg_jual').val('');
                }
            });

            $('#status_pernikahan').on('change', function() {
                if ($(this).val() === 'Menikah') {
                    $('#pasangan').stop(true, true).slideDown();
                } else {
                    $('#pasangan').stop(true, true).slideUp();
                    $('#nama_p, #nik_p').val('');
                }
            }).trigger('change');
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Mengirim...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('store.booking') }}';
            let method = 'POST';

            clearValidationUI();

            let formData = new FormData(this);
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    sessionStorage.setItem('success', 'Booking berhasil dikirimkan.');
                    spinner.addClass('d-none');
                    btnText.text('Kirim Data');
                    submitBtn.prop('disabled', false);
                    window.location.href = "{{ route('booking.sukses') }}";
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        toastr.error('Ada inputan yang salah!', 'GAGAL!', {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: 'toast-bottom-right',
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            if (key.includes('.')) {
                                let parts = key.split('.');
                                let field = parts[0];
                                let index = parseInt(parts[1]);
                                let inputSelector;

                                if ($(`[name="${field}[]"]`).length > 0) {
                                    inputSelector = $(`[name="${field}[]"]`).eq(index);
                                    appendFieldError(inputSelector, val[0]);
                                }
                                return;
                            }

                            let input = $('#' + key);
                            if (input.length) {
                                appendFieldError(input, val[0]);
                            }
                        });
                    }

                    spinner.addClass('d-none');
                    btnText.text('Kirim Data');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
