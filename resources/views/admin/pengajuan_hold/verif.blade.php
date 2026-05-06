@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3 bg-indigo text-white">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Verifikasi Data Booking</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Tanggal Booking</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->tgl_booking_formatted }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Nama Lengkap</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->nama_lengkap }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">NIK</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->nik }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Jenis
                                                Kelamin</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->jenis_kelamin }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Tempat Lahir</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->tempat_lahir }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Tanggal
                                                Lahir</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->tgl_lahir_formatted }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Alamat</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->alamat }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">NPWP</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->npwp }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="no_bpjs_kes" class="col-sm-4 col-form-label">No. BPJS Kes</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="no_bpjs_kes" value="{{ $data->no_bpjs_kes }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->email }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Nomor
                                                Telepon</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->no_telp }}" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Nama
                                                Saudara</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->nama_saudara }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">No. Telp
                                                Saudara</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->no_telp_saudara }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Nama Marketing</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->marketing->nama_marketing ?? '-' }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Lokasi Perumahan</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->lokasi->nama_kavling ?? '-' }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Blok Kavling</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->kavling->kode_kavling ?? '-' }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Jenis Properti</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->jenis_properti ?? '-' }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="hrg_jual" class="col-sm-4 col-form-label">Harga Jual</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp.</span>
                                                    </div>
                                                    <input type="text" id="hrg_jual" name="hrg_jual"
                                                        value="{{ number_format($data->hrg_jual, 0, ',', '.') }}"
                                                        class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Jenis Perumahan</label>
                                            <div class="col-sm-8">
                                                <input type="text" value="{{ $data->jenis_perumahan }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label">Foto KTP</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->foto_ktp)
                                                        <img src="{{ asset('assets/booking/' . $data->foto_ktp) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">Foto NPWP</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->foto_npwp)
                                                        <img src="{{ asset('assets/booking/' . $data->foto_npwp) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label">Foto KK</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->foto_kk)
                                                        <img src="{{ asset('assets/booking/' . $data->foto_kk) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">Foto BPJS</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->foto_bpjs)
                                                        <img src="{{ asset('assets/booking/' . $data->foto_bpjs) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label">Foto KTP Pasangan</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->foto_ktp_p)
                                                        <img src="{{ asset('assets/booking/' . $data->foto_ktp_p) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">Bukti Transfer</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->file_bukti)
                                                        <img src="{{ asset('assets/booking/' . $data->file_bukti) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label">Foto Pemohon</label>
                                                <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                    style="max-width: 350px; height: 180px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                                    @if ($data->foto_pemohon)
                                                        <img src="{{ asset('assets/booking/' . $data->foto_pemohon) }}"
                                                            style="max-width: 100%; max-height: 100%;">
                                                    @else
                                                        <span style="color: #6c757d;">Tidak ada file</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <form id="formData">
                                    @csrf
                                    <hr>

                                    <h5 class="font-weight-bold mb-4 text-danger">Pembayaran Booking</h5>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Booking
                                            Fee</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" name="booking_fee" id="booking_fee"
                                                    value="{{ number_format((float) $data->booking_fee, 0, ',', '.') }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <label class="col-sm-2 col-form-label">Tanggal Booking Fee</label>
                                        <div class="col-sm-4">
                                            <input type="date" name="tgl_booking_fee" id="tgl_booking_fee"
                                                value="{{ $data->tgl_booking_fee }}" class="form-control">
                                        </div>
                                    </div>

                                    <hr>

                                    <h5 class="font-weight-bold mb-4 text-danger">Potongan Biaya</h5>

                                    @php
                                        function formatRp($val)
                                        {
                                            return !is_null($val) && $val > 0 ? number_format($val, 0, ',', '.') : '';
                                        }
                                    @endphp

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Diskon</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="diskon" name="diskon"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->diskon) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <h5 class="font-weight-bold mb-4 text-danger">Tambahan Biaya</h5>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Pajak BPHTB</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="pajak_bphtb" name="pajak_bphtb"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->pajak_bphtb) }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <select name="stt_free_pajak_bphtb" id="stt_free_pajak_bphtb"
                                                class="form-control select-free">
                                                <option value=""></option>
                                                <option value="1"
                                                    {{ $data->stt_free_pajak_bphtb == 1 ? 'selected' : '' }}>Free</option>
                                                <option value="2"
                                                    {{ $data->stt_free_pajak_bphtb == 2 ? 'selected' : '' }}>Tidak Free
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Biaya Notaris</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="biaya_notaris" name="biaya_notaris"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->biaya_notaris) }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <select name="stt_free_biaya_notaris" id="stt_free_biaya_notaris"
                                                class="form-control select-free">
                                                <option value=""></option>
                                                <option value="1"
                                                    {{ $data->stt_free_biaya_notaris == 1 ? 'selected' : '' }}>Free
                                                </option>
                                                <option value="2"
                                                    {{ $data->stt_free_biaya_notaris == 2 ? 'selected' : '' }}>Tidak Free
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Biaya KPR</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="biaya_kpr" name="biaya_kpr"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->biaya_kpr) }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <select name="stt_free_biaya_kpr" id="stt_free_biaya_kpr"
                                                class="form-control select-free">
                                                <option value=""></option>
                                                <option value="1"
                                                    {{ $data->stt_free_biaya_kpr == 1 ? 'selected' : '' }}>Free</option>
                                                <option value="2"
                                                    {{ $data->stt_free_biaya_kpr == 2 ? 'selected' : '' }}>Tidak Free
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Biaya Lain - lain</label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp.</span>
                                                    </div>
                                                    <input type="text" id="biaya_lain_lain" name="biaya_lain_lain"
                                                        class="form-control format-number"
                                                        value="{{ formatRp($data->biaya_lain_lain) }}">
                                                </div>
                                            </div>

                                             <div class="col-sm-2">
                                                <select name="stt_free_biaya_lain" id="stt_free_biaya_lain"
                                                    class="form-control select-free">
                                                    <option value=""></option>
                                                    <option value="1"
                                                        {{ $data->stt_free_biaya_lain == 1 ? 'selected' : '' }}>Free</option>
                                                    <option value="2"
                                                        {{ $data->stt_free_biaya_lain == 2 ? 'selected' : '' }}>Tidak Free
                                                    </option>
                                                </select>
                                            </div>
                                    </div>

                                      <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Biaya Custom</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="biaya_custom" name="biaya_custom"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->biaya_custom) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">PPN</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="ppn" name="ppn"
                                                    class="form-control format-number"
                                                    value="{{ is_null($data->ppn) ? '0' : number_format((float) $data->ppn, 0, ',', '.') }}">
                                            </div>
                                        </div>

                                        <label class="col-sm-2 col-form-label"> Pajak PPH</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="pajak_pph" name="pajak_pph"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->pajak_pph) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Bonus Konsumen</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="bonus_konsumen" name="bonus_konsumen"
                                                    class="form-control format-number"
                                                    value="{{ formatRp($data->bonus_konsumen) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-5">
                                        <label class="col-sm-2 col-form-label">Total Harga Rumah</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="total_harga_rumah" name="total_harga_rumah"
                                                    class="form-control format-number" readonly
                                                    value="{{ !is_null($data->total_harga_rumah) ? number_format((float) $data->total_harga_rumah, 0, ',', '.') : number_format((float) $data->hrg_jual, 0, ',', '.') }}">
                                            </div>
                                        </div>

                                        <label class="col-sm-2 col-form-label">Total Harga Komisi</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" id="total_harga_komisi" name="total_harga_komisi"
                                                    class="form-control format-number" readonly
                                                    value="{{ !is_null($data->total_harga_komisi) ? number_format((float) $data->total_harga_komisi, 0, ',', '.') : number_format((float) $data->hrg_jual, 0, ',', '.') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <input type="hidden" id="primary_id" name="primary_id"
                                        value="{{ $data->id }}">
                                    <input type="hidden" name="booking_fee" id="booking_fee"
                                        value="{{ $data->booking_fee }}">

                                    <div class="form-group row">
                                        <label for="stt_reg" class="col-sm-2 col-form-label">Status Verifikasi</label>
                                        <div class="col-sm-3">
                                            <select name="stt_reg" id="stt_reg" class="form-control select-status">
                                                <option value=""></option>
                                                <option value="1" {{ $data->stt_reg == 1 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="2" {{ $data->stt_reg == 2 ? 'selected' : '' }}>
                                                    Disetujui
                                                </option>
                                                <option value="3" {{ $data->stt_reg == 3 ? 'selected' : '' }}>Ditolak
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Metode Bayar</label>
                                        <div class="col-sm-3">
                                            <select class="form-select select-metode-bayar" name="id_metode_bayar"
                                                id="id_metode_bayar">
                                                <option value=""></option>
                                                @foreach ($metodeBayarList as $item)
                                                    <option value="{{ $item->id }}" {{ $data->id_metode_bayar == $item->id ? 'selected' : '' }}>{{ $item->jenis_bayar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="id_bank" class="col-sm-2 col-form-label">Rekening Pembayaran</label>
                                        <div class="col-sm-4">
                                            <select class="form-select select-bank" name="id_bank" id="id_bank">
                                                <option value=""></option>
                                                @foreach ($bankList as $item)
                                                    <option value="{{ $item->id }}" {{ $data->id_bank == $item->id ? 'selected' : '' }}>{{ $item->nama }} - {{ $item->no_rek }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jenis_pembelian" class="col-sm-2 col-form-label">Jenis
                                            Pembelian</label>
                                        <div class="col-sm-3">
                                            <select name="jenis_pembelian" id="jenis_pembelian"
                                                class="form-control select-pembelian" required>
                                                <option value=""></option>
                                                <option value="Pembelian Cash"
                                                    {{ $data->jenis_pembelian == 'Pembelian Cash' ? 'selected' : '' }}>
                                                    Pembelian Cash</option>
                                                <option value="Cash Bertahap"
                                                    {{ $data->jenis_pembelian == 'Cash Bertahap' ? 'selected' : '' }}>Cash
                                                    Bertahap</option>
                                                <option value="KPR"
                                                    {{ $data->jenis_pembelian == 'KPR' ? 'selected' : '' }}>KPR</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- CASH ==================================> -->
                                    <hr class="hr-transaksi" style="display: none;">
                                    <div id="trx_cash" style="display: none;">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Atas Nama Surat</label>
                                            <div class="col-sm-3">
                                                <input name="an_surat_cash" id="an_surat_cash" class="form-control"
                                                    type="text"
                                                    value="{{ $data->an_surat_cash ?? $data->nama_lengkap }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CASH BERTAHAP ==================================> -->
                                    <hr class="hr-transaksi" style="display: none;">
                                    <div id="trx_cash_bertahap" style="display: none;">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Termin (x)</label>
                                            <div class="col-sm-2">
                                                <div class="input-group">
                                                    <input name="termin_x_cash_b" id="termin_x_cash_b"
                                                        class="form-control format-number"
                                                        value="{{ isset($data->termin_x_cash_b) && $data->termin_x_cash_b != 0 ? number_format($data->termin_x_cash_b, 0, ',', '.') : 60 }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Bulan</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer text-center">
                                        <a href="{{ route('pengajuan-hold.index') }}" class="btn btn-danger">Kembali</a>

                                        <button type="submit" class="btn btn-primary ms-1" id="submitBtn"
                                            @if (isset($data) && $data->stt_reg == 2) readonly @endif>
                                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                                aria-hidden="true"></span>
                                            <span class="button-text">Simpan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-status').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
            });

            $('.select-free').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: 'Pilih Status Free',
            });

            $('.select-jenis').select2({
                theme: "bootstrap4",
                placeholder: 'Pilih Jenis Pembelian',
                minimumResultsForSearch: Infinity,
            });

            $('.select-bank').select2({
                theme: "bootstrap4",
                placeholder: 'Pilih Bank',
                minimumResultsForSearch: Infinity,
                width: '100%'
            });

            $('.select-metode-bayar').select2({
                theme: "bootstrap4",
                placeholder: 'Pilih Metode Bayar',
                minimumResultsForSearch: Infinity,
                width: '100%'
            });

            $('.select-pembelian').select2({
                theme: "bootstrap4",
                placeholder: 'Pilih Jenis Pembelian',
                minimumResultsForSearch: Infinity,
            });

            function hideAllTransactionForms() {
                $('#trx_cash').hide();
                $('#trx_cash_bertahap').hide();
                $('.hr-transaksi').hide();
            }

            $('#jenis_pembelian').on('change', function() {
                let selected = $(this).val();
                hideAllTransactionForms();

                switch (selected) {
                    case 'Pembelian Cash':
                        $('#trx_cash').prev('.hr-transaksi').show();
                        $('#trx_cash').show();
                        break;
                    case 'Cash Bertahap':
                        $('#trx_cash_bertahap').prev('.hr-transaksi').show();
                        $('#trx_cash_bertahap').show();
                        break;
                }
            });

            $('#jenis_pembelian').trigger('change');

            function hitungTotal() {

                let hrg_jual = unformatNumber($('#hrg_jual').val());

                let diskon = unformatNumber($('#diskon').val());
                let pajak_bphtb = unformatNumber($('#pajak_bphtb').val());
                let biaya_notaris = unformatNumber($('#biaya_notaris').val());
                let biaya_kpr = unformatNumber($('#biaya_kpr').val());
                let biaya_custom = unformatNumber($('#biaya_custom').val());
                let biaya_lain = unformatNumber($('#biaya_lain_lain').val());
                let ppn = unformatNumber($('#ppn').val());
                let pajak_pph = unformatNumber($('#pajak_pph').val());
                let bonus = unformatNumber($('#bonus_konsumen').val());

                let stt_bphtb = $('#stt_free_pajak_bphtb').val();
                let stt_notaris = $('#stt_free_biaya_notaris').val();
                let stt_kpr = $('#stt_free_biaya_kpr').val();
                let stt_lain = $('#stt_free_biaya_lain').val();

                let total_rumah = hrg_jual;

                if (stt_bphtb != '1') total_rumah += pajak_bphtb;
                if (stt_notaris != '1') total_rumah += biaya_notaris;
                if (stt_kpr != '1') total_rumah += biaya_kpr;
                if (stt_lain != '1') total_rumah += biaya_lain;

                total_rumah += biaya_custom + ppn;
                total_rumah -= diskon;

                let total_komisi = hrg_jual;

                total_komisi -= pajak_pph;
                if (stt_bphtb == '1') total_komisi -= pajak_bphtb;
                if (stt_notaris == '1') total_komisi -= biaya_notaris;
                if (stt_kpr == '1') total_komisi -= biaya_kpr;
                if (stt_lain == '1') total_komisi -= biaya_lain;

                total_komisi -= bonus + diskon + ppn;

                $('#total_harga_rumah').val(formatNumber(total_rumah));
                $('#total_harga_komisi').val(formatNumber(total_komisi));
            }

            $(document).on('input',
                '#diskon, #pajak_bphtb, #biaya_notaris, #biaya_kpr, #biaya_custom, #biaya_lain_lain, #ppn, #pajak_pph, #bonus_konsumen',
                function() {
                    let input = $(this).val().replace(/[^\d]/g, '');
                    let formatted = input.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    $(this).val(formatted);
                    hitungTotal();
                });

            $(document).on('change', '#stt_free_pajak_bphtb, #stt_free_biaya_notaris, #stt_free_biaya_kpr, #stt_free_biaya_lain',
                function() {
                    hitungTotal();
                });

        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('readonly', true);

            let id = '{{ $data->id }}';
            let url = '{{ route('pengajuan-hold.verifikasi.simpan', ['id' => ':id']) }}'.replace(':id', id);
            let method = 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this);
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    sessionStorage.setItem('success', 'Verifikasi Booking Berhasil!');
                    window.location.href = "{{ route('pengajuan-hold.index') }}";
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        toastr.error("Ada inputan yang salah!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            let input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
                        });
                    } else {
                        audio.play();
                        toastr.error('Terjadi Kesalahan', "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });
                    }
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('readonly', false);
                }
            });
        });
    </script>
@endpush
