@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Data Customer</h3>
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-dark" data-toggle="modal"
                                            data-target="#modalFilterCetak">
                                            <i class="fas fa-file mr-1"></i> Cetak Data
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered w-100 table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Nasabah</th>
                                            <th>Marketing</th>
                                            <th>Perumahan</th>
                                            <th>Status Progres</th>
                                            <th width="13%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>


        <!-- /.content -->
        <div class="modal fade" id="modalFilterCetak" tabindex="-1" role="dialog" data-focus="false">
            <div class="modal-dialog" role="document">
                <form action="{{ route('customer.cetak') }}" method="GET" target="_blank">
                    <div class="modal-content">
                        <div class="modal-header bg-indigo">
                            <h5 class="modal-title text-white font-weight-bold" id="modalFilterCetakLabel">Form Cetak Data
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>Lokasi</label>
                                <select name="lokasi" class="form-control select-lokasi">
                                    <option value=""></option>
                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Opsi Cetak</label>
                                <select name="tipe" class="form-control select-tipe">
                                    <option value=""></option>
                                    <option value="1">Excel</option>
                                    <option value="0">PDF</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-cetak">
                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="btn-text">Cetak</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false"
            aria-labelledby="modalFormLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-indigo">
                        <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel">Form Customer</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formData">
                        @csrf
                        <input type="hidden" id="primary_id" name="primary_id">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Lengkap <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input name="nama_lengkap" id="nama_lengkap" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">NIK <span style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input name="nik" id="nik" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tempat Lahir <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input name="tempat_lahir" id="tempat_lahir" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">Tanggal Lahir <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input name="tgl_lahir" id="tgl_lahir" class="form-control" type="date">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">No. Telp / WA <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input name="no_telp" id="no_telp" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">Jenis Kelamin <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <select class="form-control select-jk" name="jenis_kelamin" id="jenis_kelamin">
                                        <option value=""></option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-4">
                                    <input name="email" id="email" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">NPWP</label>
                                <div class="col-sm-4">
                                    <input name="npwp" id="npwp" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Pekerjaan</label>
                                <div class="col-sm-4">
                                    <input name="pekerjaan" id="pekerjaan" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">No. BPJS Kes</label>
                                <div class="col-sm-4">
                                    <input name="no_bpjs_kes" id="no_bpjs_kes" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Alamat KTP <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-6">
                                    <textarea name="alamat_ktp" id="alamat_ktp" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Alamat Domisili <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-6">
                                    <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Status Pernikahan</label>
                                <div class="col-sm-4">
                                    <select class="form-control select-status" name="status_pernikahan"
                                        id="status_pernikahan">
                                        <option value=""></option>
                                        <option value="Belum Menikah">Belum Menikah</option>
                                        <option value="Menikah">Menikah</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Pasangan</label>
                                <div class="col-sm-4">
                                    <input name="nama_p" id="nama_p" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">NIK Pasangan</label>
                                <div class="col-sm-4">
                                    <input name="nik_p" id="nik_p" class="form-control" type="text">
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Saudara</label>
                                <div class="col-sm-4">
                                    <input name="nama_saudara" id="nama_saudara" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">No. Telp Saudara</label>
                                <div class="col-sm-4">
                                    <input name="no_telp_saudara" id="no_telp_saudara" class="form-control"
                                        type="text">
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Lokasi Perumahan</label>
                                <div class="col-sm-4">
                                    <select class="form-control select-lokasi" disabled name="id_lokasi" id="id_lokasi">
                                        <option value=""></option>
                                        @foreach ($lokasi as $l)
                                            <option value="{{ $l->id }}">{{ $l->nama_kavling }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-sm-2">Blok/Kav</label>
                                <div class="col-sm-4">
                                    <select name="id_kavling" id="id_kavling" disabled
                                        class="form-control select-kavling"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Harga Rumah</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" name="hrg_jual" id="hrg_jual"
                                            class="form-control format-number" readonly>
                                    </div>
                                </div>
                                 <label class="control-label col-sm-2">Jenis Properti <span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <select class="form-select select-jenis-properti" name="jenis_properti" id="jenis_properti">
                                        <option value=""></option>
                                        <option value="Ruko">Ruko</option>
                                        <option value="Kavling">Kavling</option>
                                    </select>
                                </div>
                            </div>

                           <div class="form-group row">
                                <label class="col-sm-2 col-form-label">DP</label>
                                <div class="col-sm-4">
                                    <div id="dp-container"></div>
                                </div>

                                <label class="control-label col-sm-2">Booking Fee</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" name="booking_fee" id="booking_fee"
                                            class="form-control format-number">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5 class="font-weight-bold mb-4 text-danger">Potongan Biaya</h5>

                            <div class="form-group row mb-3">
                                <label class="col-sm-2 col-form-label">Diskon</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" id="diskon" name="diskon"
                                            class="form-control format-number">
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
                                            class="form-control format-number">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <select name="stt_free_pajak_bphtb" id="stt_free_pajak_bphtb"
                                        class="form-control select-free">
                                        <option value=""></option>
                                        <option value="1">Free</option>
                                        <option value="2">Tidak Free</option>
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
                                            class="form-control format-number">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <select name="stt_free_biaya_notaris" id="stt_free_biaya_notaris"
                                        class="form-control select-free">
                                        <option value=""></option>
                                        <option value="1">Free</option>
                                        <option value="2">Tidak Free</option>
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
                                            class="form-control format-number">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <select name="stt_free_biaya_kpr" id="stt_free_biaya_kpr"
                                        class="form-control select-free">
                                        <option value=""></option>
                                        <option value="1">Free</option>
                                        <option value="2">Tidak Free</option>
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
                                            class="form-control format-number">
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Biaya Lain - lain</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" id="biaya_lain_lain" name="biaya_lain_lain"
                                            class="form-control format-number">
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
                                            class="form-control format-number">
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label"> Pajak PPH</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" id="pajak_pph" name="pajak_pph"
                                            class="form-control format-number">
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
                                            class="form-control format-number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-2 col-form-label">Total Harga Rumah</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" id="total_harga_rumah" name="total_harga_rumah"
                                            class="form-control format-number" readonly>
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Total Harga Komisi</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" id="total_harga_komisi" name="total_harga_komisi"
                                            class="form-control format-number" readonly>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Marketing</label>
                                <div class="col-sm-4">
                                    <select class="form-control select-marketing" disabled name="id_marketing"
                                        id="id_marketing">
                                        <option value=""></option>
                                        @foreach ($marketing as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_marketing }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-sm-2">Marketing Agent</label>
                                <div class="col-sm-4">
                                    <select class="form-control select-agent" disabled name="id_agent" id="id_agent">
                                        <option value=""></option>
                                        @foreach ($agent as $f)
                                            <option value="{{ $f->id }}">{{ $f->nama_agent }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Perumahan</label>
                                <div class="col-sm-4">
                                    <select class="form-control select-jp" disabled name="jenis_perumahan"
                                        id="jenis_perumahan">
                                        <option value=""></option>
                                        <option value="Subsidi">Subsidi</option>
                                        <option value="Komersil">Komersil</option>
                                    </select>
                                </div>
                                <label class="control-label col-sm-2">Jenis Pembelian</label>
                                <div class="col-sm-4">
                                    <select class="form-control select-pembelian" disabled name="jenis_pembelian"
                                        id="jenis_pembelian">
                                        <option value=""></option>
                                        <option value="Pembelian Cash">Pembelian Cash</option>
                                        <option value="Cash Bertahap">Cash Bertahap</option>
                                        <option value="KPR">KPR</option>
                                    </select>
                                </div>
                            </div>

                            <!-- CASH ==================================> -->
                            <hr class="hr-transaksi" style="display: none;">
                            <div id="trx_cash" style="display: none;">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Atas Nama Surat</label>
                                    <div class="col-sm-4">
                                        <input name="an_surat_cash" id="an_surat_cash" class="form-control"
                                            type="text" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- CASH BERTAHAP ==================================> -->
                            <hr class="hr-transaksi" style="display: none;">
                            <div id="trx_cash_bertahap" style="display: none;">

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Termin (x)</label>
                                    <div class="col-sm-4">
                                        <input name="termin_x_cash_b" id="termin_x_cash_b"
                                            class="form-control format-number" type="number" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="button-text">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function renderDP(dpList) {
            let container = $('#dp-container');
            container.empty();

            if (!dpList || dpList.length === 0) {
                container.append('<small class="text-muted">Tidak ada data DP</small>');
                return;
            }

            dpList.forEach(function(item, index) {
                container.append(`
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">DP ${index + 1}</span>
                        </div>

                        <input type="hidden" name="dp_id[]" value="${item.id}">

                        <input type="text"
                            name="dp[]"
                            class="form-control format-number"
                            value="${formatNumber(item.nominal)}">
                    </div>
                `);
            });
        }


        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Lokasi",
            });
            $('.select-jk').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Kelamin",
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
            });
            $('.select-marketing').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Marketing",
            });

            $('.select-agent').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Agent",
            });

            $('.select-jp').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Perumahan",
            });

            $('.select-pembelian').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Pembelian",
            });

            $('.select-status').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
            });

            $('.select-free').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status Free",
                minimumResultsForSearch: Infinity,
            });

            $('.select-jenis-properti').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Properti",
                minimumResultsForSearch: Infinity,
            });

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

                let total_rumah = hrg_jual;

                if (stt_bphtb != '1') total_rumah += pajak_bphtb;
                if (stt_notaris != '1') total_rumah += biaya_notaris;
                if (stt_kpr != '1') total_rumah += biaya_kpr;

                total_rumah += biaya_custom + biaya_lain + ppn;
                total_rumah -= diskon;

                let total_komisi = hrg_jual;

                total_komisi -= pajak_pph;
                if (stt_bphtb == '1') total_komisi -= pajak_bphtb;
                if (stt_notaris == '1') total_komisi -= biaya_notaris;
                if (stt_kpr == '1') total_komisi -= biaya_kpr;

                total_komisi -= bonus + diskon + ppn + biaya_lain;

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

            $(document).on('change', '#stt_free_pajak_bphtb, #stt_free_biaya_notaris, #stt_free_biaya_kpr',
                function() {
                    hitungTotal();
                });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            hideAllTransactionForms();
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#jenis_kelamin').val('').trigger('change');
            $('#status').val('').trigger('change');
            $('#id_lokasi').val('').trigger('change');
            $('#id_kavling').val('').trigger('change');
            $('#id_marketing').val('').trigger('change');
            $('#id_agent').val('').trigger('change');
            $('#jenis_pembelian').val('').trigger('change');

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#modalFilterCetak').on('show.bs.modal', function() {
            $('.select-tipe').val('').trigger('change');
            $('.select-lokasi').val('').trigger('change');
        });

        let isEditMode = false;

        $(document).on('click', '.edit-button', function() {
            isEditMode = true;

            var url = $(this).data('url');
            $.get(url, function(response) {
                if (response.status === 'success') {
                    const data = response.data;

                    $('#primary_id').val(data.id);
                    $('#tgl_booking').val(response.data.tgl_booking_formatted);
                    $('#nama_lengkap').val(data.nama_lengkap);
                    $('#nik').val(data.nik);
                    $('#nik_p').val(data.nik_p);
                    $('#tempat_lahir').val(data.tempat_lahir);
                    $('#tgl_lahir').val(data.tgl_lahir);
                    $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
                    $('#no_telp').val(data.no_telp);
                    $('#email').val(data.email);
                    $('#npwp').val(data.npwp);
                    $('#no_bpjs_kes').val(data.no_bpjs_kes);
                    $('#alamat_ktp').val(data.alamat_ktp);
                    $('#alamat_domisili').val(data.alamat_domisili);
                    $('#pekerjaan').val(data.pekerjaan);
                    $('#status_pernikahan').val(data.status_pernikahan).trigger('change');
                    $('#nama_p').val(data.nama_p);
                    $('#nama_saudara').val(data.nama_saudara);
                    $('#no_telp_saudara').val(data.no_telp_saudara);


                    $('#id_lokasi').val(data.id_lokasi).trigger('change');
                    setTimeout(function() {
                        $('#id_kavling').val(data.id_kavling).trigger('change');
                    }, 500);

                    $('#hrg_jual').val(formatNumber(data.hrg_jual));
                    $('#diskon').val(formatNumber(data.diskon));
                    $('#pajak_bphtb').val(formatNumber(data.pajak_bphtb));
                    $('#biaya_notaris').val(formatNumber(data.biaya_notaris));
                    $('#biaya_kpr').val(formatNumber(data.biaya_kpr));
                    $('#biaya_custom').val(formatNumber(data.biaya_custom));
                    $('#biaya_lain_lain').val(formatNumber(data.biaya_lain_lain));
                    $('#ppn').val(formatNumber(data.ppn));
                    $('#pajak_pph').val(formatNumber(data.pajak_pph));
                    $('#bonus_konsumen').val(formatNumber(data.bonus_konsumen));
                    $('#total_harga_rumah').val(formatNumber(data.total_harga_rumah));
                    $('#total_harga_komisi').val(formatNumber(data.total_harga_komisi));
                    renderDP(data.dp_list);
                    $('#booking_fee').val(formatNumber(data.booking_fee));
                    $('#stt_free_pajak_bphtb').val(data.stt_free_pajak_bphtb).trigger('change');
                    $('#stt_free_biaya_notaris').val(data.stt_free_biaya_notaris).trigger('change');
                    $('#stt_free_biaya_kpr').val(data.stt_free_biaya_kpr).trigger('change');
                    $('#id_marketing').val(data.id_marketing).trigger('change');
                    $('#id_agent').val(data.id_agent).trigger('change');
                    $('#jenis_pembelian').val(data.jenis_pembelian).trigger('change');
                    $('#jenis_perumahan').val(data.jenis_perumahan).trigger('change');
                    $('#jenis_properti').val(data.jenis_properti).trigger('change');

                    $('#modalForm').modal('show');

                }
            });
        });

        $(document).on('change', '#jenis_pembelian', function() {
            const val = $(this).val();

            $('#trx_cash, #trx_cash_bertahap, .hr-transaksi').hide();

            if (val === 'Pembelian Cash') {
                $('#trx_cash').show();
                $('#trx_cash').prev('.hr-transaksi').show();
            }

            if (val === 'Cash Bertahap') {
                $('#trx_cash_bertahap').show();
                $('#trx_cash_bertahap').prev('.hr-transaksi').show();
            }
        });


        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });

            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });

            const routeGetKavling = "{{ route('customer.getKavling', ':id') }}";
            const routeGetHarga = "{{ route('customer.getHargaKavling', ':id') }}";

            $('#id_lokasi').on('change', function() {
                let idLokasi = $(this).val();

                if (!isEditMode) {
                    $('#hrg_jual, #biaya_surat, #biaya_lain, #total_harga').val('');
                }

                if (idLokasi) {
                    const urlKavling = routeGetKavling.replace(':id', idLokasi);
                    $.get(urlKavling, function(data) {
                        let options = '<option value=""></option>';
                        data.forEach(function(item) {
                            options +=
                                `<option value="${item.id}">${item.kode_kavling}</option>`;
                        });
                        $('#id_kavling').html(options);

                        if (isEditMode) {
                            $('#id_kavling').val($('#id_kavling').data('selected')).trigger(
                                'change.select2');
                        }
                    });
                }
            });

            $('.btn-cetak').on('click', function(e) {
                e.preventDefault();

                let btn = $(this);
                let form = $('#modalFilterCetak form');
                let tipe = form.find('.select-tipe').val();
                let formData = form.serialize();

                if (!tipe) {
                    alert('Pilih opsi cetak dulu');
                    return;
                }

                btn.prop('disabled', true);
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.btn-text').text('Loading...');

                if (tipe == "0") {
                    let url = form.attr('action') + "?" + formData;
                    window.open(url, '_blank');

                    btn.prop('disabled', false);
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.btn-text').text('Cetak');
                    $('#modalFilterCetak').modal('hide');

                } else {
                    $.ajax({
                        url: form.attr('action'),
                        method: "GET",
                        data: formData,
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(data, status, xhr) {
                            let disposition = xhr.getResponseHeader('Content-Disposition');
                            let filename = "export.xlsx";
                            if (disposition && disposition.indexOf('filename=') !== -1) {
                                let matches = /filename="?(.+)"?/.exec(disposition);
                                if (matches != null && matches[1]) filename = matches[1];
                            }

                            let url = window.URL.createObjectURL(data);
                            let a = document.createElement('a');
                            a.href = url;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();

                            btn.prop('disabled', false);
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.btn-text').text('Cetak');
                            $('#modalFilterCetak').modal('hide');
                        },
                        error: function() {
                            alert('Gagal mencetak Excel, coba lagi.');

                            btn.prop('disabled', false);
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.btn-text').text('Cetak');
                        }
                    });
                }
            });
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.replace(/\D/g, '') // hanya angka
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // ribuan
        }

        const uangFields = [
            '#pembayaran_booking',
            '#diskon_cash',
            '#pembayaran_cash',
            '#sisa_bayar_ajb',
            '#dp_cash_b',
            '#dp_kredit',
            '#cicilan_kredit'
        ];

        uangFields.forEach(function(selector) {
            $(document).on('input', selector, function() {
                let val = $(this).val().replace(/\./g, '');
                $(this).val(formatRupiah(val));
            });
        });



        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = id ? '{{ route('customer.update', ['customer' => ':id']) }}'.replace(':id', id) :
                '{{ route('customer.store') }}';
            let method = id ? 'PUT' : 'POST';

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
                success: function(response) {
                    $('#modalForm').modal('hide');
                    audio.play();
                    let msg = id ? "Data Customer berhasil diupdate!" :
                        "Data Customer berhasil ditambahkan!";
                    toastr.success(msg, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    $('.data-table').DataTable().ajax.reload();
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

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                },
                complete: function() {
                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });


        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                ajax: "{{ route('customer.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_terima',
                        name: 'tgl_terima',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'id_marketing',
                        name: 'id_marketing',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_lokasi',
                        name: 'id_lokasi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_status_progres',
                        name: 'id_status_progres',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn,
                        className: 'text-center'
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }]
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            if (!form.has('button.delete-button').length) return;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Hapus</span>',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: false,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML = `
                    <span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span>
                    Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function() {
                                audio.play();
                                toastr.success("Data telah dihapus!", "BERHASIL", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                $('.data-table').DataTable().ajax.reload(null,
                                    false);
                                Swal.close();
                            },
                            error: function() {
                                audio.play();
                                toastr.error("Gagal menghapus data.", "GAGAL!", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                btnText.innerHTML = `Ya, Hapus`;
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });
    </script>
@endpush
