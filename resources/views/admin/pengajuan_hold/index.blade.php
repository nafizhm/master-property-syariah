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
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Data Pengajuan Hold</h3>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('booking') }}" class="btn btn-sm btn-dark mr-1" target="_blank">
                                            <i class="fas fa-calendar-plus mr-1"></i> Booking
                                        </a>
                                        <a href="{{ route('pengajuan-hold.arsip') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-archive mr-1"></i> Arsip Pengajuan Hold
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="30px">No</th>
                                            <th>Customer</th>
                                            <th>Marketing</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th class="text-center" width="200px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel">Edit Customer Booking</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Booking <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-2">
                                <input type="date" class="form-control" id="tgl_booking" name="tgl_booking">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Nama Lengkap <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <input name="nama_lengkap" id="nama_lengkap" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">NIK <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <input name="nik" id="nik" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Tempat Lahir <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <input name="tempat_lahir" id="tempat_lahir" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">Tanggal Lahir <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <input name="tgl_lahir" id="tgl_lahir" class="form-control" type="date">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">No. Telp / WA <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <input name="no_telp" id="no_telp" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">Jenis Kelamin <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-select select-jk" name="jenis_kelamin" id="jenis_kelamin">
                                    <option value=""></option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Email</label>
                            <div class="col-sm-4">
                                <input name="email" id="email" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">NPWP</label>
                            <div class="col-sm-3">
                                <input name="npwp" id="npwp" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Pekerjaan</label>
                            <div class="col-sm-4">
                                <input name="pekerjaan" id="pekerjaan" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">No. BPJS Kes</label>
                            <div class="col-sm-3">
                                <input name="no_bpjs_kes" id="no_bpjs_kes" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Alamat KTP <span style="color: red;">*</span></label>
                            <div class="col-sm-6">
                                <textarea name="alamat_ktp" id="alamat_ktp" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3">Alamat Domisili <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-6">
                                <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Status Pernikahan</label>
                            <div class="col-sm-4">
                                <select class="form-select select-status" name="status_pernikahan"
                                    id="status_pernikahan">
                                    <option value=""></option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                    <option value="Menikah">Menikah</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Nama Pasangan</label>
                            <div class="col-sm-4">
                                <input name="nama_p" id="nama_p" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">NIK Pasangan</label>
                            <div class="col-sm-3">
                                <input name="nik_p" id="nik_p" class="form-control" type="text">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Nama Saudara</label>
                            <div class="col-sm-4">
                                <input name="nama_saudara" id="nama_saudara" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">No. Telp Saudara</label>
                            <div class="col-sm-3">
                                <input name="no_telp_saudara" id="no_telp_saudara" class="form-control" type="text">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Lokasi Perumahan <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-select select-lokasi" name="id_lokasi" id="id_lokasi">
                                    <option value=""></option>
                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Blok/Kav <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select name="id_kavling" id="id_kavling" class="form-select select-kavling"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Harga Rumah</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="hrg_jual" id="hrg_jual"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="control-label col-sm-2">Jenis Properti <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-select select-jenis-properti" name="jenis_properti"
                                    id="jenis_properti">
                                    <option value=""></option>
                                    <option value="Ruko">Ruko</option>
                                    <option value="Kavling">Kavling</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Marketing <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-select select-marketing" name="id_marketing" id="id_marketing">
                                    <option value=""></option>
                                    @foreach ($marketing as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_marketing }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Marketing Agent</label>
                            <div class="col-sm-3">
                                <select class="form-select select-agent" name="id_agent" id="id_agent">
                                    <option value=""></option>
                                    @foreach ($agent as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama_agent }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Jenis Perumahan <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-select select-jp" name="jenis_perumahan" id="jenis_perumahan">
                                    <option value=""></option>
                                    <option value="Subsidi">Subsidi</option>
                                    <option value="Komersil">Komersil</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Jenis Pembelian <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-select select-pembelian" name="jenis_pembelian" id="jenis_pembelian">
                                    <option value=""></option>
                                    <option value="Pembelian Cash">Pembelian Cash</option>
                                    <option value="Cash Bertahap">Cash Bertahap</option>
                                    <option value="KPR">KPR</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Booking Fee <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input name="booking_fee" id="booking_fee" class="form-control format-number"
                                        type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                            <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
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
            const successMsg = sessionStorage.getItem('success');
            if (successMsg) {
                audio.play();
                toastr.success(successMsg, "BERHASIL", {
                    progressBar: true,
                    timeOut: 3500,
                    positionClass: "toast-bottom-right",
                });
                sessionStorage.removeItem('success');
            }
        });

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

            $('.select-jenis-properti').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Properti",
            });
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $('#status').on('change', function() {
            if ($(this).val() === 'Menikah') {
                $('#pasangan').show();
                $('#nama_p, #nik_p').prop('required', true);
            } else {
                $('#pasangan').hide();
                $('#nama_p, #nik_p').prop('required', false);
            }
        }).trigger('change');

        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                responsive: true,
                ordering: false,
                ajax: "{{ route('pengajuan-hold.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap',
                        searchable: true
                    },
                    {
                        data: 'nama_marketing',
                        name: 'nama_marketing',
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling',
                    },

                    {
                        data: 'stt_reg',
                        name: 'stt_reg',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        visible: showActionColumn
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

        const routeGetKavling = "{{ route('pengajuan-hold.getKavling', ':id') }}";
        const routeGetHarga = "{{ route('pengajuan-hold.getHargaKavling', ':id') }}";

        const setOptions = (data) => {
            let options = '<option value=""></option>';
            data.forEach(i => options += `<option value="${i.id}">${i.kode_kavling}</option>`);
            return options;
        };

        $(document).on('click', '.edit-button', function() {
            const url = $(this).data('url');

            $.get(url, function(res) {
                if (res.status !== 'success') return;

                const d = res.data;

                $('#primary_id').val(d.id);
                $('#tgl_booking').val(d.tgl_booking);
                $('#nama_lengkap').val(d.nama_lengkap);
                $('#nik').val(d.nik);
                $('#nik_p').val(d.nik_p);
                $('#tempat_lahir').val(d.tempat_lahir);
                $('#tgl_lahir').val(d.tgl_lahir);
                $('#jenis_kelamin').val(d.jenis_kelamin).trigger('change');
                $('#no_telp').val(d.no_telp);
                $('#email').val(d.email);
                $('#npwp').val(d.npwp);
                $('#no_bpjs_kes').val(d.no_bpjs_kes);
                $('#booking_fee').val(formatNumber(d.booking_fee));
                $('#alamat_ktp').val(d.alamat_ktp);
                $('#alamat_domisili').val(d.alamat_domisili);
                $('#pekerjaan').val(d.pekerjaan);
                $('#status_pernikahan').val(d.status_pernikahan).trigger('change');
                $('#nama_p').val(d.nama_p);
                $('#nama_saudara').val(d.nama_saudara);
                $('#no_telp_saudara').val(d.no_telp_saudara);

                $('#id_marketing').val(d.id_marketing).trigger('change');
                $('#id_agent').val(d.id_agent).trigger('change');
                $('#jenis_pembelian').val(d.jenis_pembelian).trigger('change');
                $('#jenis_properti').val(d.jenis_properti).trigger('change');
                $('#jenis_perumahan').val(d.jenis_perumahan).trigger('change');

                $('#hrg_jual').val(formatNumber(d.hrg_jual));
                $('#id_lokasi').val(d.id_lokasi).trigger('change.select2');

                $.get(routeGetKavling.replace(':id', d.id_lokasi), function(kavlings) {
                    $('#id_kavling').html(setOptions(kavlings));
                    $('#id_kavling').val(d.id_kavling).trigger('change.select2');
                });

                $('#modalForm').modal('show');
            });
        });

        $(document).ready(function() {

            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi"
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling"
            });

            $('#id_lokasi').on('change', function() {
                const id = $(this).val();

                $('#hrg_jual').val('');
                $('#id_kavling').html('<option value=""></option>');

                if (!id) return;

                $.get(routeGetKavling.replace(':id', id), function(data) {
                    $('#id_kavling').html(setOptions(data));
                });
            });

            $('#id_kavling').on('change', function() {
                const id = $(this).val();

                if (!id) {
                    $('#hrg_jual').val('');
                    return;
                }

                $.get(routeGetHarga.replace(':id', id), function(data) {
                    $('#hrg_jual').val(formatNumber(data.hrg_jual));
                });
            });

        });

        $('#modalForm').on('hidden.bs.modal', function() {
            isEditMode = false;
            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('.form-select').val('').trigger('change');

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            isEditMode = false;
            selectedKavlingEdit = null;

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
            let url = id ? '{{ route('pengajuan-hold.update', ['pengajuan_hold' => ':id']) }}'.replace(':id',
                    id) :
                '{{ route('pengajuan-hold.store') }}';
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
                    let msg = id ? "Hold berhasil diupdate!" : "Hold berhasil ditambahkan!";
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

        // Hapus data
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

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

                        btnText.innerHTML =
                            `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Menghapus...`;
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

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
@endpush
