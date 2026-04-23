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
                                    <h3 class="font-weight-bold text-lg">Surat Pemesanan Rumah (SPR)</h3>
                                    <div class="d-flex align-items-center">
                                        @if ($permissions['tambah'])
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modalForm"><i class="fas fa-plus"></i>
                                                Tambah SPR</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered w-100 table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Customer</th>
                                            <th>Lokasi Rumah</th>
                                            <th class="text-center" width="15%">Action</th>
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false" data-modal-type="">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel">Form SPR</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Customer</label>
                            <div class="col-sm-8">
                                <select name="id_customer" id="id_customer" class="form-select select-customer">
                                    <option value=""></option>
                                    @foreach ($customerList as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_lengkap }} ({{ $m->kode_customer }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Perumahan</label>
                            <div class="col-sm-4">
                                <input type="text" name="nama_perum" id="nama_perum" class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label">Kode Kavling</label>
                            <div class="col-sm-3">
                                <input type="text" name="kode_kavling" id="kode_kavling" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Customer</label>
                            <div class="col-sm-4">
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label">No. Telp</label>
                            <div class="col-sm-3">
                                <input type="text" name="no_telp" id="no_telp" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Alamat KTP</label>
                            <div class="col-sm-4">
                                <textarea id="alamat_ktp" name="alamat_ktp" class="form-control" rows="3"></textarea>
                            </div>
                            <label class="col-sm-2 col-form-label">No. KTP</label>
                            <div class="col-sm-3">
                                <input type="text" name="nik" id="nik" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Pekerjaan</label>
                            <div class="col-sm-4">
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label">Estimasi Pendapatan</label>
                            <div class="col-sm-4">
                                <select name="estimasi_pendapatan" id="estimasi_pendapatan"
                                    class="form-select select-estimasi-pendapatan">
                                    <option value=""></option>
                                    <option value="< Rp10.000.000">
                                        < Rp10.000.000</option>
                                    <option value="Rp10.000.001 - Rp15.000.000">Rp10.000.001 - Rp15.000.000</option>
                                    <option value="Rp15.000.001 - Rp20.000.000">Rp15.000.001 - Rp20.000.000</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Join Income</label>
                            <div class="col-sm-4">
                                <select name="join_income" id="join_income" class="form-select select-join-income">
                                    <option value=""></option>
                                    <option value="Rp20.000.001 - Rp25.000.000">Rp20.000.001 - Rp25.000.000</option>
                                    <option value=">Rp25.000.000">>Rp25.000.000</option>
                                </select>
                            </div>
                            <label class="col-sm-2 col-form-label">Sumber Dana</label>
                            <div class="col-sm-4">
                                <select name="sumber_dana" id="sumber_dana" class="form-select select-sumber-dana">
                                    <option value=""></option>
                                    <option value="Gaji">Gaji</option>
                                    <option value="Usaha">Usaha</option>
                                    <option value="Tabungan">Tabungan</option>
                                    <option value="Warisan">Warisan</option>
                                    <option value="Investasi">Investasi</option>
                                    <option value="Lain-lain">Lain-lain</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Tujuan Pembelian</label>
                            <div class="col-sm-4">
                                <select name="tujuan_pembelian" id="tujuan_pembelian"
                                    class="form-select select-tujuan-pembelian">
                                    <option value=""></option>
                                    <option value="Tempat Tinggal/Pribadi">Tempat Tinggal/Pribadi</option>
                                    <option value="Investasi">Investasi</option>
                                    <option value="Sewa">Sewa</option>
                                    <option value="Lain - lain">Lain - lain</option>
                                </select>
                            </div>
                            <label class="col-sm-2 col-form-label">Pembelian Rumah ke</label>
                            <div class="col-sm-4">
                                <select name="pembelian_rumah_ke" id="pembelian_rumah_ke"
                                    class="form-select select-pembelian-rumah-ke">
                                    <option value=""></option>
                                    <option value="1">1</option>
                                    <option value="2, dst">2, dst</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Proyek</label>
                            <div class="col-sm-4">
                                <input type="text" name="nama_proyek" id="nama_proyek" class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label">Tipe Bangunan</label>
                            <div class="col-sm-2">
                                <input type="text" name="tipe_bangunan" id="tipe_bangunan"
                                    class="form-control format-number">

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Luas Tanah</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="luas_tanah" id="luas_tanah"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Luas Bangunan</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="luas_bangunan" id="luas_bangunan"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Marketing</label>
                            <div class="col-sm-4">
                                <input type="text" name="nama_marketing" id="nama_marketing" class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label">PIC</label>
                            <div class="col-sm-3">
                                <input type="text" name="pic" id="pic" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Harga Jual Standard</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="hrg_jual_std" id="hrg_jual_std"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Diskon</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="diskon" id="diskon"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Biaya KPR</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="biaya_kpr" id="biaya_kpr"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Biaya Custom</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="biaya_custom" id="biaya_custom"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Biaya Lain</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="biaya_lain" id="biaya_lain"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Total Harga</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="total_harga_unit" id="total_harga_unit"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-2 col-form-label">Metode Pembayaran</label>

                            <div class="col-sm-4">
                                <select name="metode_pembayaran" id="metode_pembayaran"
                                    class="form-select select-metode-pembayaran">
                                    <option value=""></option>
                                    <option value="HARD CASH">HARD CASH</option>
                                    <option value="SOFT CASH">SOFT CASH</option>
                                    <option value="KPR">KPR</option>
                                </select>
                            </div>

                            <div class="col-sm-3 d-none" id="group_soft">
                                <div class="input-group">
                                    <input type="text" name="termin_soft" id="termin_soft"
                                        class="form-control format-number">
                                    <span class="input-group-text">Bulan</span>
                                </div>
                            </div>

                            <div class="col-sm-3 d-none" id="group_kpr">
                                <div class="input-group">
                                    <input type="text" name="termin_kpr" id="termin_kpr"
                                        class="form-control format-number">
                                    <span class="input-group-text">Tahun</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Harga Jual</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="hrg_jual" id="hrg_jual"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Booking Fee</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="booking_fee" id="booking_fee"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">DP</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="dp" id="dp"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Kewajiban Kredit</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="kewajiban_kredit" id="kewajiban_kredit"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Catatan Keterangan</label>
                            <div class="col-sm-4">
                                <textarea id="catatan" name="catatan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary " id="submitBtn">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-customer').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
            });

            $('.select-estimasi-pendapatan').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Estimasi Pendapatan",
                minimumResultsForSearch: Infinity,
            });

            $('.select-join-income').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Join Income",
                minimumResultsForSearch: Infinity,
            });

            $('.select-sumber-dana').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Sumber Dana",
                minimumResultsForSearch: Infinity,
            });

            $('.select-tujuan-pembelian').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Tujuan Pembelian",
                minimumResultsForSearch: Infinity,
            });

            $('.select-pembelian-rumah-ke').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Pembelian Rumah ke",
                minimumResultsForSearch: Infinity,
            });

            $('.select-metode-pembayaran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Metode Pembayaran",
                minimumResultsForSearch: Infinity,
            });
        });

        const detailUrl = "{{ route('spr.detail', ':id') }}";

        $('#id_customer').on('change', function() {
            let id = $(this).val();
            if (!id) return;

            let url = detailUrl.replace(':id', id);

            $.get(url, function(res) {

                let c = res.customer;

                $('#nama_lengkap').val(c.nama_lengkap);
                $('#nama_lengkap').val(c.nama_lengkap);
                $('#alamat_ktp').val(c.alamat_ktp);
                $('#nik').val(c.nik);
                $('#no_telp').val(c.no_telp);
                $('#pekerjaan').val(c.pekerjaan);

                $('#hrg_jual_std').val(formatNumber(c.hrg_jual));
                $('#hrg_jual').val(formatNumber(c.hrg_jual));
                $('#diskon').val(formatNumber(c.diskon));

                if (c.kavling) {
                    $('#kode_kavling').val(c.kavling.kode_kavling);
                    $('#luas_tanah').val(c.kavling.luas_tanah);
                    $('#luas_bangunan').val(c.kavling.luas_bangunan);
                    $('#tipe_bangunan').val(c.kavling.tipe_bangunan);
                }

                if (c.lokasi) {
                    $('#nama_perum').val(c.lokasi.nama_kavling);
                    $('#nama_proyek').val(c.lokasi.nama_kavling);
                }

                if (c.marketing) {
                    $('#nama_marketing').val(c.marketing.nama_marketing);
                }

                $('#biaya_kpr').val(formatNumber(c.biaya_kpr));
                $('#biaya_custom').val(formatNumber(c.biaya_custom));
                $('#biaya_lain').val(formatNumber(c.biaya_lain_lain));

                $('#total_harga_unit').val(formatNumber(c.total_harga_rumah));

                $('#booking_fee').val(formatNumber(res.booking_fee));
                $('#dp').val(formatNumber(res.dp));

            });
        });

        $('#metode_pembayaran').on('change', function() {
            let val = $(this).val();

            $('#group_soft').addClass('d-none');
            $('#group_kpr').addClass('d-none');

            if (val === 'SOFT CASH') {
                $('#group_soft').removeClass('d-none');
            } else if (val === 'KPR') {
                $('#group_kpr').removeClass('d-none');
            }
        });

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                ajax: "{{ route('spr.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'customer.nama_lengkap',
                        name: 'customer.nama_lengkap',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'lokasi_rumah',
                        name: 'lokasi_rumah',
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
                }, ]
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $('#primary_id').val('');
            $('.form-select').val('').trigger('change');

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
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
            let url = id ? '{{ route('spr.update', ['spr' => ':id']) }}'.replace(':id', id) :
                '{{ route('spr.store') }}';
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
                    let msg = id ? "SPR berhasil diupdate!" : "SPR berhasil ditambahkan!";
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
                }
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'SPR ini akan dihapus secara permanen!',
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

                        btnText.innerHTML =
                            `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function() {
                                audio.play();
                                toastr.success("SPR telah dihapus!", "BERHASIL", {
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
