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
                                    <h3 class="font-weight-bold text-lg">Data Kavling</h3>
                                    <div class="d-flex align-items-center">
                                        <a id="btnPdf" href="{{ route('kavling.cetakPdf', ['id_lokasi' => '__ID__']) }}"
                                            target="_blank" class="btn btn-danger btn-sm mr-1">
                                            <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                        </a>

                                        <a id="btnExcel"
                                            href="{{ route('kavling.cetakExcel', ['id_lokasi' => '__ID__']) }}"
                                            target="_blank" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-excel mr-1"></i> Cetak Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <label class="col-sm-1 col-form-label">Lokasi</label>
                                    <div class="col-sm-3">
                                        <select name="id_lokasi" id="id_lokasi" class="form-select select-lokasi">
                                            <option value="0">Semua</option>
                                            @foreach ($lokasiList as $l)
                                                <option value="{{ $l->id }}"
                                                    {{ request('id_lokasi') == $l->id ? 'selected' : '' }}>
                                                    {{ $l->nama_kavling }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped small data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Perumahan</th>
                                            <th>Kode Kavling</th>
                                            <th>Panjang</th>
                                            <th>Lebar</th>
                                            <th>Luas</th>
                                            <th>Rincian Harga</th>
                                            <th>Total Harga</th>
                                            <th width="10%">Action</th>
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

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Perumahan</label>
                            <div class="col-sm-4">
                                <input type="text" name="nama_kavling" id="nama_kavling" class="form-control" readonly>
                            </div>
                            <label class="col-sm-2 col-form-label">Kode Kavling</label>
                            <div class="col-sm-3">
                                <input type="text" name="kode_kavling" id="kode_kavling" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Panjang Kanan</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="panjang_kanan" id="panjang_kanan"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m</span>
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Panjang Kiri</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="panjang_kiri" id="panjang_kiri"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Lebar Depan</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="lebar_depan" id="lebar_depan"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m</span>
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Lebar Belakang</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="lebar_belakang" id="lebar_belakang"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Luas Tanah</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="luas_tanah" id="luas_tanah"
                                        class="form-control format-decimal">
                                    <div class="input-group-append">
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Luas Bangunan</label>
                            <div class="col-sm-3">
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
                            <label class="col-sm-3 col-form-label">Harga per Meter</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="hrg_meter" id="hrg_meter"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipe Rumah</label>
                            <div class="col-sm-2">
                                <input type="text" name="tipe_bangunan" id="tipe_bangunan"
                                    class="form-control format-number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Harga Jual</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="hrg_jual" id="hrg_jual"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Biaya Surat</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="biaya_surat" id="biaya_surat"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Peningkatan Mutu</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="biaya_lain" id="biaya_lain"
                                        class="form-control format-number">
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Daya Listrik</label>
                            <div class="col-sm-3">
                                <input type="text" name="daya_listrik" id="daya_listrik"
                                    class="form-control format-number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Keterangan</label>
                            <div class="col-sm-4">
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
                            </div>
                            <label class="col-sm-2 col-form-label">No. Sertif</label>
                            <div class="col-sm-3">
                                <input type="text" name="no_sertifikat" id="no_sertifikat" class="form-control">
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

    <div class="modal fade" id="modalUpload" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalUploadLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalUploadLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formUpload" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_upload" name="primary_upload">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Foto</label>
                            <div class="col-sm-8">
                                <input type="file" name="foto" id="foto" accept=".jpg, .jpeg, .png, .webp">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-8">
                                <div class="img-thumbnail mb-2 d-flex align-items-center justify-content-center"
                                    id="previewFoto"
                                    style="max-width: 600px; height: 400px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                    <span style="color: #6c757d;">Tidak ada foto</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-1" id="submitUpload">
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
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });
        });


        $('#foto').on('change', function() {
            const file = this.files[0];
            const previewDiv = $('#previewFoto');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.html(
                        `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%;">`);
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.html('<span style="color: #6c757d;">Tidak ada foto</span>');
            }
        });

        document.querySelectorAll('.format-decimal').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = input.value;
                value = value.replace(/[^0-9.]/g, '');
                const parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts[1];
                }
                if (parts.length === 2) {
                    parts[1] = parts[1].slice(0, 1);
                    value = parts[0] + '.' + parts[1];
                }
                input.value = value;
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
                ajax: {
                    url: "{{ route('kavling.index') }}",
                    data: function(d) {
                        d.id_lokasi = $('#id_lokasi').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'id_lokasi',
                        name: 'id_lokasi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_kavling',
                        name: 'kode_kavling',
                        orderable: false,
                        searchable: true,
                    },
                    {
                        data: 'panjang',
                        name: 'panjang',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lebar',
                        name: 'lebar',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'luas',
                        name: 'luas',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'rincian_harga',
                        name: 'rincian_harga',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_harga',
                        name: 'total_harga',
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
            $('#id_lokasi').on('change', function() {
                let idLokasi = $(this).val();

                let pdfRoute = "{{ route('kavling.cetakPdf', ['id_lokasi' => '__ID__']) }}";
                let excelRoute = "{{ route('kavling.cetakExcel', ['id_lokasi' => '__ID__']) }}";

                $('#btnPdf').attr('href', pdfRoute.replace('__ID__', idLokasi));
                $('#btnExcel').attr('href', excelRoute.replace('__ID__', idLokasi));

                table.ajax.reload();
            });
        });


        $(document).on('click', '.foto-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    let data = response.data;
                    $('#modalUploadLabel').text('Foto Kavling');
                    $('#primary_upload').val(data.id);
                    let foto = response.data.foto;
                    let preview = $('#previewFoto');
                    if (foto) {
                        let imageUrl = '/assets/foto_kavling/' + foto;
                        preview.html(
                            `<img src="${imageUrl}" alt="Foto Kavling" style="max-height: 100%; max-width: 100%;">`
                        );
                    } else {
                        preview.html(`<span style="color: #6c757d;">Tidak ada foto</span>`);
                    }

                    $('#modalUpload').modal('show');
                }
            });
        });

        $(document).on('click', '.edit-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    let data = response.data;

                    // Fungsi untuk format angka
                    const formatNumber = (number) => {
                        let num = parseFloat(number);
                        return isNaN(num) ? '' : num.toLocaleString('id-ID');
                    };

                    $('#modalFormLabel').text('Edit Kavling');
                    $('#primary_id').val(data.id);
                    $('#nama_kavling').val(data.lokasi.nama_kavling);
                    $('#kode_kavling').val(data.kode_kavling);
                    $('#panjang_kanan').val(data.panjang_kanan);
                    $('#panjang_kiri').val(data.panjang_kiri);
                    $('#lebar_depan').val(data.lebar_depan);
                    $('#lebar_belakang').val(data.lebar_belakang);
                    $('#luas_tanah').val(data.luas_tanah);
                    $('#luas_bangunan').val(data.luas_bangunan);
                    $('#hrg_meter').val(formatNumber(data.hrg_meter));
                    $('#tipe_bangunan').val(formatNumber(data.tipe_bangunan));
                    $('#hrg_jual').val(formatNumber(data.hrg_jual));
                    $('#biaya_surat').val(formatNumber(data.biaya_surat));
                    $('#biaya_lain').val(formatNumber(data.biaya_lain));
                    $('#daya_listrik').val(formatNumber(data.daya_listrik));

                    $('#keterangan').val(data.keterangan);
                    $('#no_sertifikat').val(data.no_sertifikat);

                    $('#modalForm').modal('show');
                }
            });
        });

       $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.form-select').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $('#primary_id').val('');

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        });

        $('#modalUpload').on('hidden.bs.modal', function() {
            $('#formUpload')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitUpload');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);

            $('#previewFoto').html('<span style="color: #6c757d;">Tidak ada foto</span>');
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
            let url = id ? '{{ route('kavling.update', ['kavling' => ':id']) }}'.replace(':id',
                    id) :
                '{{ route('kavling.store') }}';
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
                    let msg = id ? "Kavling berhasil diupdate!" :
                        "Kavling berhasil ditambahkan!";
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

        $('#formUpload').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitUpload');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_upload').val();
            let url = '{{ route('kavling.foto-update', ['id' => ':id']) }}'.replace(':id', id);
            let method = 'PUT';

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
                    $('#modalUpload').modal('hide');
                    audio.play();
                    let msg = "Foto Kavling berhasil diupload!";
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
    </script>
@endpush
