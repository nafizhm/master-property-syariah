@extends('admin.layout_admin')

@push('css')
    <style>
        .photo-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .photo-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .photo-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 8px;
            pointer-events: none;
        }

        .photo-container:hover .photo-overlay {
            opacity: 1;
        }

        .zoom-icon {
            color: white;
            font-size: 24px;
        }

        .modal-photo-viewer {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .no-photos {
            padding: 60px 20px;
            text-align: center;
            color: #6c757d;
        }

        .no-photos i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .error-message {
            padding: 40px 20px;
            text-align: center;
            color: #dc3545;
        }

        .error-message i {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .photo-counter {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .modal-lg-custom {
            max-width: 90%;
        }

        @media (max-width: 768px) {
            .modal-lg-custom {
                max-width: 95%;
                margin: 10px;
            }

            .photo-thumbnail {
                height: 150px;
            }
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
                                    <h3 class="font-weight-bold text-lg text-white">Rekap Opname Proyek Jalan</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nama Proyek</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="nama_proyek" name="nama_proyek" class="form-control"
                                            readonly value="{{ $proyek->nama_proyek }}">
                                    </div>
                                    <label class="col-sm-1 col-form-label">OP Ke</label>
                                    <div class="col-sm-2">
                                        <input type="number" id="op_ke" name="op_ke" class="form-control" readonly
                                            value="{{ $proyekDetail->op_ke }}">
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
                                            value="{{ $proyekDetail->tanggal }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Foto Opname</label>
                                    <div class="col-sm-3">
                                        <button class="btn btn-md btn-info lihat-lampiran" data-id="{{ $proyekDetail->id }}"
                                            style="cursor:pointer;" data-title="Foto Opname">
                                            Lihat Foto
                                        </button>
                                    </div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalLampiranLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalLampiranLabel">Foto Opname</h5>
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

    <div class="modal fade" id="photoPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0 bg-indigo">
                    <h5 class="modal-title text-white" id="photoPreviewTitle">Preview Foto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="photoPreviewImage" src="" class="modal-photo-viewer" alt="Preview Foto">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
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
                ajax: "{{ route('proyek-jalan.detail', $proyekDetail->id) }}",
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

        const fotoCache = {};
        const CACHE_TIMEOUT_MS = 2 * 60 * 60 * 1000;

        $(document).on('click', '.lihat-lampiran', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');

            $('#modalLampiranLabel').html('<i class="fas fa-camera mr-2"></i>' + title);

            $('#modalLampiranContent').html(`
                <div class="text-center py-5">
                    <div class="loading-spinner mb-3"></div>
                    <p class="text-muted">Memuat foto...</p>
                </div>
            `);

            if (!$('#modalLampiran').hasClass('show')) {
                $('#modalLampiran').modal('show');
            }

            const now = Date.now();

            if (fotoCache[id] && now - fotoCache[id].timestamp < CACHE_TIMEOUT_MS) {
                const cachedData = fotoCache[id].data;

                if (cachedData.length > 0) {
                    displayPhotos(cachedData);
                } else {
                    displayNoPhotos();
                }
                return;
            }

            $.ajax({
                url: `{{ route('getFoto.jalan', ':id') }}`.replace(':id', id),
                method: 'GET',
                success: function(res) {
                    if (res.status === 'success') {
                        fotoCache[id] = {
                            data: res.data,
                            timestamp: now
                        };

                        if (res.data.length > 0) {
                            displayPhotos(res.data);
                        } else {
                            displayNoPhotos();
                        }
                    } else {
                        displayNoPhotos();
                    }
                },
                error: function() {
                    displayError();
                }
            });
        });

        function displayPhotos(photos) {
            let content = `<div class="row">`;

            photos.forEach((photo, index) => {
                content += `
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4" data-id="${photo.id}">
                        <div class="photo-container position-relative">
                            <img src="${photo.url}" class="photo-thumbnail img-fluid border"
                                alt="Foto Opname ${index + 1}"
                                onclick="showPhotoPreview('${photo.url}', 'Foto Opname ${index + 1}')">
                            <div class="photo-overlay">
                                <i class="fas fa-search-plus zoom-icon"></i>
                            </div>
                            <div class="photo-counter">${index + 1}</div>
                        </div>
                        <div class="text-center mt-2">
                            <button class="btn btn-sm btn-outline-danger btn-delete-foto" data-id="${photo.id}" id="btn-delete-foto-${photo.id}">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                                aria-hidden="true"></span>
                                <i class="fas fa-trash mr-1"></i> Hapus Foto
                            </button>
                        </div>
                    </div>
                `;
            });

            content += `</div>`;
            content += `
    <div class="text-center mt-3">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Total ${photos.length} foto | Klik foto untuk memperbesar
        </small>
    </div>
    `;

            $('#modalLampiranContent').html(content);
        }

        function displayNoPhotos() {
            $('#modalLampiranContent').html(`
            <div class="no-photos">
                <i class="fas fa-images"></i>
                <h5>Tidak Ada Foto</h5>
                <p class="mb-0">Belum ada foto yang tersedia untuk item ini.</p>
            </div>
        `);
        }

        function displayError() {
            $('#modalLampiranContent').html(`
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <h5>Gagal Memuat Foto</h5>
                <p class="mb-0">Terjadi kesalahan saat mengambil data foto. Silakan coba lagi.</p>
                <button class="btn btn-outline-danger btn-sm mt-3" onclick="location.reload()">
                    <i class="fas fa-refresh mr-2"></i>Muat Ulang
                </button>
            </div>
        `);
        }

        window.showPhotoPreview = function(imageUrl, title) {
            $('#photoPreviewImage').attr('src', imageUrl);
            $('#photoPreviewTitle').text(title);
            $('#photoPreviewModal').modal('show');
        };

        $(document).keydown(function(e) {
            if ($('#photoPreviewModal').hasClass('show')) {
                if (e.key === 'Escape') {
                    $('#photoPreviewModal').modal('hide');
                }
            }
        });

        $(document).on('click', '.btn-delete-foto', function(e) {
            e.preventDefault();

            const fotoId = $(this).data('id');
            const parentCard = $(this).closest('.col-lg-4');

            let submitBtn = $('#btn-delete-foto-' + fotoId);
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.btn-text');

            spinner.removeClass('d-none');
            btnText.text('Menghapus...');
            submitBtn.prop('disabled', true);

            if (!confirm('Yakin ingin menghapus foto ini?')) return;

            $.ajax({
                url: `{{ route('hapusFoto.jalan', ':id') }}`.replace(':id', fotoId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.status === 'success') {
                        parentCard.remove();
                        audio.play();
                        toastr.success("Foto berhasil dihapus!", "SUKSES!", {
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: "toast-bottom-right",
                        });
                    } else {
                        audio.play();
                        toastr.error("foto gagal dihapus!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 3500,
                            positionClass: "toast-bottom-right",
                        });

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function() {
                    audio.play();
                    toastr.error("Terjadi kesalahan saat menghapus foto!", "GAGAL!", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
