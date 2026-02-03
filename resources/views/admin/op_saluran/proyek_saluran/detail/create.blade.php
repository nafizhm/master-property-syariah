@extends('admin.layout_admin')

@push('css')
    <style>
        .preview-foto {
            max-width: 200px;
            max-height: 200px;
            min-width: 100px;
            object-fit: contain;
            border-radius: 8px;
            border: 2px dashed #ddd;
            padding: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .preview-wrapper {
            position: relative;
            display: inline-block;
        }

        .remove-btn {
            position: absolute;
            top: -6px;
            right: 3px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            text-align: center;
            line-height: 18px;
            font-size: 14px;
            cursor: pointer;
            z-index: 10;
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
                            <div class="card-header p-3 bg-warning">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg text-white">Tambah Opname Proyek Saluran</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formData">
                                    @csrf
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Nama Proyek</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="nama_proyek" name="nama_proyek" class="form-control"
                                                readonly value="{{ $proyek->nama_proyek }}">
                                        </div>
                                        <label class="col-sm-1 col-form-label">OP Ke</label>
                                        <div class="col-sm-2">
                                            <input type="number" id="op_ke" name="op_ke" class="form-control"
                                                readonly value="{{ $op_ke }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">No. Kontrak</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="no_kontrak" name="no_kontrak" class="form-control"
                                                value="{{ $proyek->no_kontrak }}" readonly>
                                        </div>
                                        <label class="col-sm-1 col-form-label">Tanggal</label>
                                        <div class="col-sm-2">
                                            <input type="date" id="tanggal" name="tanggal" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Foto Opname</label>
                                        <button type="button" id="tambahFoto" class="btn btn-md bg-indigo"
                                            data-toggle="modal" data-target="#modalFoto">
                                            <i class="fa fa-plus"></i> Tambah Foto
                                        </button>
                                    </div>

                                    <table class="table table-bordered data-table w-100">
                                        <thead>
                                            <tr class="table-warning">
                                                <th width="30px">No</th>
                                                <th>Jenis Pekerjaan</th>
                                                <th width="200px">Presentase</th>
                                                <th width="200px">OP Lalu</th>
                                                <th width="200px">OP Sekarang</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" align="center"><strong>Jumlah</strong></td>
                                                <td align="right"><strong>100.00 %</strong></td>
                                                <td align="right"><strong id="totalOpLalu">0 %</strong></td>
                                                <td align="right"><strong id="totalOpSekarang">0 %</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" align="center"><strong>Total Keseluruhan</strong></td>
                                                <td align="right" class="table-warning"><strong id="finalOpTotal">0
                                                        %</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>


                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
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

    <div class="modal fade" id="modalFoto" tabindex="-1" role="dialog" aria-labelledby="modalFotoLabel" area-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <input type="file" class="form-control-file" id="foto" name="foto[]" multiple
                                    accept=".jpg,.jpeg,.png" onchange="previewFoto(this)">
                                <small class="form-text text-muted">
                                    Format: JPG, PNG, GIF. Maksimal 2MB
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preview Foto</label>
                                <div id="preview-container" class="d-flex flex-wrap gap-2 position-relative">
                                    <div id="no-preview"
                                        class="preview-foto d-flex align-items-center justify-content-center text-muted">
                                        <i class="fas fa-image fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clearFoto">Clear</button>
                    <button type="button" class="btn btn-primary" id="simpanFoto">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                paging: false,
                searching: false,
                info: false,
                ajax: "{{ route('proyek-saluran.detail-create', $proyek->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'presentasi',
                        name: 'presentasi'
                    },
                    {
                        data: 'op_lalu',
                        name: 'op_lalu'
                    },
                    {
                        data: 'op_sekarang',
                        name: 'op_sekarang'
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                }],
                drawCallback: function() {
                    hitungTotal();
                }
            });

            $(document).on('input', '.op-sekarang', function() {
                var row = $(this).closest('tr');
                var val = parseFloat($(this).val()) || 0;
                var opLaluText = row.find('td:eq(3)').text().replace('%', '').trim();
                var presentasiText = row.find('td:eq(2)').text().replace('%', '').trim();
                var opLalu = parseFloat(opLaluText) || 0;
                var presentasi = parseFloat(presentasiText) || 0;

                if (val > (100 - opLalu)) {
                    alert('OP Sekarang tidak boleh melebihi sisa dari 100% dikurangi OP Lalu (' + (100 -
                        opLalu) + '%)');
                    $(this).val(100 - opLalu);
                }

                hitungTotal();
            });

            function hitungTotal() {
                var totalOpLalu = 0;
                var totalOpSekarang = 0;

                $('.data-table tbody tr').each(function() {
                    var presentasi = parseFloat($(this).find('td:eq(2)').text().replace('%', '').trim()) ||
                        0;
                    var opLalu = parseFloat($(this).find('td:eq(3)').text().replace('%', '').trim()) || 0;
                    var opSekarang = parseFloat($(this).find('input.op-sekarang').val()) || 0;

                    totalOpLalu += (presentasi * opLalu / 100);
                    totalOpSekarang += (presentasi * opSekarang / 100);
                });

                $('#totalOpLalu').text(totalOpLalu.toFixed(2) + ' %');
                $('#totalOpSekarang').text(totalOpSekarang.toFixed(2) + ' %');
                $('#finalOpTotal').text((totalOpLalu + totalOpSekarang).toFixed(2) + ' %');
            }
        });

        let selectedFiles = [];

        function previewFoto(input) {
            const container = $('#preview-container');
            const files = Array.from(input.files);

            files.forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    x('Ukuran maksimal 2MB');
                    return;
                }

                selectedFiles.push(file);

                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = $('<div class="preview-wrapper me-2 mb-2"></div>');

                    const img = $('<img>', {
                        src: e.target.result,
                        class: 'preview-foto',
                        alt: 'Preview'
                    });

                    const removeBtn = $('<button class="remove-btn" type="button">&times;</button>');
                    removeBtn.on('click', function() {
                        const index = selectedFiles.indexOf(file);
                        if (index > -1) {
                            selectedFiles.splice(index, 1);
                            wrapper.remove();

                            if (selectedFiles.length === 0) {
                                container.append(`
                            <div id="no-preview" class="preview-foto d-flex align-items-center justify-content-center text-muted">
                                <i class="fas fa-image fa-2x"></i>
                            </div>
                        `);
                            }
                        }
                    });

                    wrapper.append(removeBtn).append(img);
                    container.append(wrapper);
                };

                reader.readAsDataURL(file);
            });

            $('#no-preview').remove();
            input.value = '';
        }

        $('#clearFoto').on('click', function() {
            selectedFiles = [];

            const container = $('#preview-container');
            container.empty();

            container.append(`
                <div id="no-preview" class="preview-foto d-flex align-items-center justify-content-center text-muted">
                    <i class="fas fa-image fa-2x"></i>
                </div>
            `);

            $('#foto').val('');
        });

        $('#simpanFoto').on('click', function() {
            $('#modalFoto').modal('hide');
        });

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('proyek-saluran.detail-store', $proyek->id) }}';
            let method = 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            if (selectedFiles.length === 0) {
                audio.play();
                toastr.error("Ada inputan yang salah!", "GAGAL!", {
                    progressBar: true,
                    timeOut: 3500,
                    positionClass: "toast-bottom-right",
                });

                $('#tambahFoto').addClass('is-invalid');
                if ($('#tambahFoto').next('.invalid-feedback').length === 0) {
                    $('#tambahFoto').after(`
                    <div class="invalid-feedback d-block ml-2">
                        Foto wajib diunggah minimal 1.
                    </div>
                `);
                }

                spinner.addClass('d-none');
                btnText.text('Simpan');
                submitBtn.prop('disabled', false);
                return;
            }

            let formData = new FormData(this);
            selectedFiles.forEach((file, index) => {
                formData.append(`foto[]`, file);
            });
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    sessionStorage.setItem('success', 'Data berhasil ditambahkan!');
                    window.location.href = "{{ route('proyek-saluran.show', $proyek->id) }}";
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
                            if (key.startsWith('foto')) {
                                $('#tambahFoto').addClass('is-invalid');
                                if ($('#tambahFoto').next('.invalid-feedback').length === 0) {
                                    $('#tambahFoto').after(`
                                    <div class="invalid-feedback d-block">
                                        ${val[0]}
                                    </div>
                                `);
                                }
                            } else {
                                let input = $('#' + key);
                                input.addClass('is-invalid');
                                input.parent().find('.invalid-feedback').remove();
                                input.parent().append(`
                                    <span class="invalid-feedback" role="alert"><strong>${val[0]}</strong></span>
                                `);
                            }
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
