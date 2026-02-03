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
                                    <h3 class="font-weight-bold text-lg">Customer</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-3 col-form-label">Nama Nasabah</label>
                                    <div class="col-sm-3">
                                        <select class="form-control select-customer" id="id_customer" name="id_customer">
                                            <option value=""></option>
                                            @foreach ($customer as $c)
                                                <option value="{{ $c->id }}">{{ $c->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-3 col-form-label">NIK</label>
                                    <div class="col-sm-3">
                                        <input type="number" name="nik" class="form-control" disabled>
                                    </div>
                                    <label class="col-sm-2 col-form-label">No. Telp</label>
                                    <div class="col-sm-3">
                                        <input type="number" name="no_telp" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="lokasi_perumahan" class="col-sm-3 col-form-label">Lokasi Perumahan</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="lokasi_perumahan" disabled>
                                    </div>
                                    <label class="col-sm-2 col-form-label">Lokasi Kav/Blok</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="kode_kavling" class="form-control" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Data File Berkas Customer</h3>
                                    <div class="d-flex align-items-center">
                                        <button type="button" id="btnUploadFile" class="btn btn-md btn-primary"
                                            data-toggle="modal" data-target="#modalForm" disabled>
                                            <i class="fas fa-upload"></i> Upload File
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="30px">No</th>
                                            <th>Nama Berkas</th>
                                            <th>File Berkas</th>
                                            <th width="100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel">Form Upload File</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Berkas</label>
                            <div class="col-sm-8">
                                <input type="text" name="nama_file" id="nama_file" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">File Berkas</label>
                            <div class="col-sm-8">
                                <input type="file" name="lampiran" id="lampiran" accept=".jpg, .jpeg, .png, .pdf">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-8">
                                <div class="img-thumbnail mb-2 d-flex align-items-center justify-content-center"
                                    id="previewFoto"
                                    style="max-width: 140px; height: 150px; background-color: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
                                    <span style="color: #6c757d;">Tidak ada File</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
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
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalLampiranLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title" id="modalLampiranLabel">File Berkas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modalLampiranContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            function toggleUploadBtn() {
                let selected = $('#id_customer').val();
                if (!selected) {
                    $('#btnUploadFile').attr('disabled', true);
                } else {
                    $('#btnUploadFile').removeAttr('disabled');
                }
            }

            toggleUploadBtn();

            $('#id_customer').on('change', function() {
                toggleUploadBtn();
            });
        });


        $(document).ready(function() {
            $('.select-customer').select2({
                theme: "bootstrap4",
                width: '100%',
                placeholder: "Pilih Customer",
            });
        });



        $(function() {
            var table;

            function loadTable(id_customer) {
                if (table) {
                    table.destroy();
                    $('.data-table tbody').empty();
                }

                table = $('.data-table').DataTable({
                    processing: false,
                    serverSide: false,
                    responsive: true,
                    ordering: false,
                    ajax: {
                        url: "{{ route('upload-file.edit', ['upload_file' => 'REPLACE_ID']) }}".replace(
                            'REPLACE_ID', id_customer),
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            type: 'files'
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
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
                            data: 'nama_file',
                            name: 'nama_file',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'lampiran',
                            name: 'lampiran',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let url = "{{ asset('assets/customer') }}/" + data;
                                return `
                            <button class="btn btn-info btn-sm lihat-lampiran" data-src="${url}" data-title="${row.nama_file}">View</button>
                            <a href="${url}" download class="btn btn-success btn-sm ml-2">Download</a>
                        `;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    columnDefs: [{
                        targets: 0,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }],
                    language: {
                        emptyTable: "Tidak ada data tersedia"
                    }
                });
            }

            $('#id_customer').on('change', function() {
                let id = $(this).val();

                if (!id) {
                    $('input[name="nik"]').val('');
                    $('input[name="no_telp"]').val('');
                    $('input[name="lokasi_perumahan"]').val('');
                    $('input[name="kode_kavling"]').val('');
                    if (table) {
                        table.clear().draw();
                    }
                    return;
                }

                $.ajax({
                    url: "{{ route('upload-file.edit', ['upload_file' => 'REPLACE_ID']) }}".replace(
                        'REPLACE_ID', id),
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('input[name="nik"]').val(res.nik || '');
                            $('input[name="no_telp"]').val(res.no_telp || '');
                            $('input[name="lokasi_perumahan"]').val(res.lokasi_perumahan || '');
                            $('input[name="kode_kavling"]').val(res.lokasi_kav_blok || '');

                            loadTable(id);
                        }
                    }
                });
            });

            $(document).on('click', '.lihat-lampiran', function() {
                let src = $(this).data('src');

                let ext = src.split('.').pop().toLowerCase();

                let content = '';
                if (ext === 'pdf') {
                    content = `<iframe src="${src}" width="100%" height="600px"></iframe>`;
                } else {
                    content = `<img src="${src}" alt="Lampiran" class="img-fluid rounded shadow">`;
                }

                $('#modalLampiranContent').html(content);
                $('#modalLampiran').modal('show');
            });
        });

        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Upload File');
        });

        $('#lampiran').on('change', function() {
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
                previewDiv.html('<span style="color: #6c757d;">Tidak ada File</span>');
            }
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);

            $('#previewFoto').html('<span style="color: #6c757d;">Tidak ada File</span>');
        });


        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#id_customer').val();
            let url = '{{ route('upload-file.update', ['upload_file' => ':id']) }}'.replace(':id', id);
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
                    $('#modalForm').modal('hide');
                    audio.play();
                    let msg = "Upload File Berhasil!";
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
    </script>
@endpush
