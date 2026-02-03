@extends('admin.layout_admin')
@push('css')
    <style>
        .qr-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .url-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 12px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            word-break: break-all;
            margin-bottom: 15px;
        }

        .copy-feedback {
            color: #28a745;
            font-size: 12px;
            margin-left: 5px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .copy-feedback.show {
            opacity: 1;
        }

        #qrCodeImage {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
@endpush
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
                                    <h3 class="font-weight-bold text-lg">Data Serah Terima Kunci</h3>
                                    <div class="d-flex align-items-center" style="gap: 3px">
                                        @if ($permissions['tambah'])
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#modalForm">
                                                <i class="fas fa-plus"></i> Tambah Serah Terima Kunci
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="50px">No</th>
                                            <th>Lokasi Rumah</th>
                                            <th>Customer</th>
                                            <th>Tgl Serah Terima</th>
                                            <th>Tgl Akhir Komplen</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th width="100px">Action</th>
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
                <form id="formData">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="tgl_serah_terima" class="col-sm-3 col-form-label">Tanggal Pindah</label>
                            <div class="col-sm-5">
                                <input type="date" name="tgl_serah_terima" id="tgl_serah_terima" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_customer" class="col-sm-3 col-form-label">Nama Customer</label>
                            <div class="col-sm-5">
                                <select class="form-control select-customer" id="id_customer" name="id_customer">
                                    <option value=""></option>
                                    @foreach ($customers as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                            <div class="col-sm-7">
                                <textarea name="alamat" id="alamat" class="form-control" readonly></textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Lokasi Perumahan</label>
                            <div class="col-sm-4">
                                <input type="text" id="nama_lokasi" name="nama_lokasi" class="form-control" readonly>
                                <input type="hidden" name="nama_lokasi" id="nama_lokasi_hidden">
                            </div>
                            <label class="col-sm-1 col-form-label">Blok</label>
                            <div class="col-sm-2">
                                <input type="text" name="kode_kavling" id="kode_kavling" class="form-control" readonly>
                                <input type="hidden" name="kode_kavling" id="kode_kavling_hidden">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                            <div class="col-sm-7">
                                <textarea type="text" id="keterangan" class="form-control" name="keterangan"></textarea>
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

    <div class="modal fade" id="modalQR" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalQRLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalQRLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- QR Code Container -->
                    <div id="qrCodeContainer"></div>

                    <!-- Action Buttons -->
                    <div class="qr-actions" style="display: none;">
                        <button type="button" class="btn btn-success btn-sm" id="copyUrlBtn">
                            <i class="fas fa-copy"></i> Copy URL
                            <span class="copy-feedback" id="copyFeedback">Copied!</span>
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="downloadQrBtn">
                            <i class="fas fa-download"></i> Download QR
                        </button>
                    </div>
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
        $(document).ready(function() {
            $('.select-customer').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
            });
        });

        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Tambah Serah Terima Kunci');
        });

        let qrUrl = '';

        $(document).on('click', '.btn-qr-code', function() {
            let id_customer = $(this).data('id_customer');
            qrUrl = `${window.location.origin}/form-aduan/${id_customer}`;

            $('#modalQR .modal-title').text('Loading QR Code...');
            $('#modalQR .modal-body #qrCodeContainer').html(
                '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Generating QR Code...</div>');
            $('#modalQR .modal-body .qr-actions').hide();
            $('#modalQR').modal('show');

            $.ajax({
                url: `/generate-qr-code`,
                method: 'GET',
                data: {
                    url: qrUrl
                },
                success: function(res) {
                    $('#modalQR .modal-title').text('QR Code Form Aduan');
                    $('#modalQR .modal-body #qrCodeContainer').html(res.qr);
                    $('#modalQR .modal-body #qrUrl').text(qrUrl);
                    $('#modalQR .modal-body .qr-actions').show();
                },
                error: function(xhr, status, error) {
                    $('#modalQR .modal-body #qrCodeContainer').html(
                        '<div class="text-center text-danger">Gagal memuat QR Code.</div>');
                }
            });
        });

        $(document).on('click', '#copyUrlBtn', function() {
            navigator.clipboard.writeText(qrUrl).then(function() {
                $('#copyFeedback').addClass('show');
                setTimeout(function() {
                    $('#copyFeedback').removeClass('show');
                }, 2000);
            }).catch(function(err) {
                const textArea = document.createElement('textarea');
                textArea.value = qrUrl;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                $('#copyFeedback').addClass('show');
                setTimeout(function() {
                    $('#copyFeedback').removeClass('show');
                }, 2000);
            });
        });

        $(document).on('click', '#downloadQrBtn', function() {
            const svgElement = $('#qrCodeContainer').find('svg')[0];

            if (!svgElement) {
                alert('QR Code SVG not found');
                return;
            }

            const svgData = new XMLSerializer().serializeToString(svgElement);
            const svgBlob = new Blob([svgData], {
                type: 'image/svg+xml;charset=utf-8'
            });
            const url = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function(blob) {
                    const pngUrl = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = pngUrl;
                    a.download = 'qr-code-form-aduan.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(pngUrl);
                }, 'image/png');

                URL.revokeObjectURL(url);
            };

            img.onerror = function() {
                alert('Gagal mengonversi QR ke gambar.');
                URL.revokeObjectURL(url);
            };

            img.src = url;
        });

        $('#modalQR').on('hidden.bs.modal', function() {
            qrUrl = '';
            $('#qrCodeContainer').empty();
            $('.qr-actions').hide();
        });

        $(document).ready(function() {
            $('#id_customer').change(function() {
                const customerId = $(this).val();

                if (customerId) {
                    $.ajax({
                        url: `/serah-terima-kunci/nasabah/${customerId}`,
                        type: 'GET',
                        success: function(res) {
                            $('#alamat').val(res.alamat ?? '');
                            $('#nama_lokasi').val(res.nama_lokasi ?? '');
                            $('#nama_lokasi_hidden').val(res.nama_lokasi ?? '');
                            $('#kode_kavling').val(res.kode_kavling ?? '');
                            $('#kode_kavling_hidden').val(res.kode_kavling ?? '');
                        },
                        error: function() {
                            $('#alamat').val('');
                            $('#nama_lokasi').val('');
                            $('#nama_lokasi_hidden').val('');
                            $('#kode_kavling').val('');
                            $('#kode_kavling_hidden').val('');
                        }
                    });
                } else {
                    $('#alamat').val('');
                    $('#nama_lokasi').val('');
                    $('#nama_lokasi_hidden').val('');
                    $('#kode_kavling').val('');
                    $('#kode_kavling_hidden').val('');
                }
            });
        });

        function modalAction(url = '') {
            $('#myModalContent').html(
                '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden"></span></div></div>'
            );
            $('#myModal').modal('show');

            $('#myModalContent').load(url);
        }


        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tgl_serah_terima').value = today;

        document.getElementById('tgl_serah_terima').addEventListener('change', function() {
            const selectedDate = this.value;
        });


        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                ajax: "{{ route('serah-terima-kunci.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lokasi_kavling',
                        name: 'lokasi_kavling',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'tgl_serah_terima',
                        name: 'tgl_serah_terima',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_expired',
                        name: 'tgl_expired',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false
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
                }, ]
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            let tanggal = $('#tgl_serah_terima').val();

            $('#formData')[0].reset();
            $('#primary_id').val('');
            $('#id_customer').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $('#tgl_serah_terima').val(tanggal);

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

            let url = '{{ route('serah-terima-kunci.store') }}';
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
                success: function(response) {
                    $('#modalForm').modal('hide');
                    audio.play();
                    toastr.success("Data telah disimpan!", "BERHASIL", {
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
