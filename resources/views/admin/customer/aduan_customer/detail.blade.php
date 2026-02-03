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
        <section class="content">
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-12">
                        <form id="formData" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header px-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h3 class="font-weight-bold text-lg">Detail Aduan</h3>
                                    </div>
                                </div>
                                <input type="hidden" id="primary_id" name="primary_id" value="{{ $aduan->id }}">

                                <div class="card-body">
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Nomer Aduan</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" value="{{ $aduan->no_aduan ?? '-' }}"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Nama Customer</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control"
                                                value="{{ $aduan->nasabah->nama_lengkap ?? '-' }}" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Lokasi / Blok</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control"
                                                value="{{ $aduan->kavling->kode_kavling ?? '-' }}" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-2 col-form-label">Isi Aduan</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="isi_aduan" id="isi_aduan" disabled>{{ $aduan->isi_aduan }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Lampiran</label>
                                        <div class="col-sm-3">
                                            <a type="button" class="btn btn-md btn-info lihat-lampiran-detail"
                                                data-id="{{ $aduan->id }}" style="cursor:pointer;" data-title="Lampiran">
                                                Lihat Foto
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('admin.customer.aduan_customer.proses_pengaduan.card')
                        </form>
                    </div>
                </div>
        </section>
    </div>

    <div class="modal fade" id="modalLampiran" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="modalLampiranLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalLampiranLabel">Lampiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <div class="modal-header border-0">
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
        $(document).ready(function() {
            $('.select-proses').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Proses",
            });
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

        const fotoCache = {};
        const CACHE_TIMEOUT_MS = 2 * 60 * 60 * 1000;

        function handleLihatLampiran(selector, routeName, cachePrefix) {
            $(document).on('click', selector, function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const cacheKey = `${cachePrefix}-${id}`;

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

                if (fotoCache[cacheKey] && now - fotoCache[cacheKey].timestamp < CACHE_TIMEOUT_MS) {
                    const cachedData = fotoCache[cacheKey].data;
                    cachedData.length > 0 ? displayPhotos(cachedData) : displayNoPhotos();
                    return;
                }

                $.ajax({
                    url: routeName.replace(':id', id),
                    method: 'GET',
                    success: function(res) {
                        if (res.status === 'success') {
                            fotoCache[cacheKey] = {
                                data: res.data,
                                timestamp: now
                            };
                            res.data.length > 0 ? displayPhotos(res.data) : displayNoPhotos();
                        } else {
                            displayNoPhotos();
                        }
                    },
                    error: displayError
                });
            });
        }

        handleLihatLampiran(
            '.lihat-lampiran-detail',
            `{{ route('getFoto.aduan', ':id') }}`,
            'detail'
        );

        handleLihatLampiran(
            '.lihat-lampiran-proses',
            `{{ route('getFoto.prosesAduan', ':id') }}`,
            'proses'
        );

        function displayPhotos(photos) {
            let content = `<div class="row">`;

            photos.forEach((photo, index) => {
                content += `
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4" data-id="${photo.id}">
                        <div class="photo-container position-relative">
                            <img src="${photo.url}" class="photo-thumbnail img-fluid border"
                                alt="Lampiran ${index + 1}"
                                onclick="showPhotoPreview('${photo.url}', 'Lampiran ${index + 1}')">
                            <div class="photo-overlay">
                                <i class="fas fa-search-plus zoom-icon"></i>
                            </div>
                            <div class="photo-counter">${index + 1}</div>
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

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Menyimpan...');
            submitBtn.prop('disabled', true);

            let id = $('#primary_id').val();
            let url = '{{ route('aduan-customer.update', ['aduan_customer' => ':id']) }}'.replace(':id', id);
            let method = 'PUT';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this);
            selectedFiles.forEach((file, index) => {
                formData.append(`fotoProses[]`, file);
            });
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    audio.play();
                    toastr.success("Data berhasil diupdate!", "BERHASIL!", {
                        progressBar: true,
                        timeOut: 1500,
                        positionClass: "toast-bottom-right",
                    });

                    setTimeout(() => {
                        window.location.href = "{{ route('aduan-customer.index') }}";
                    }, 1500);

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
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
                            if (key.startsWith('fotoProses')) {
                                let fotoInput = $('#fotoProses');
                                fotoInput.addClass('is-invalid');
                                if (fotoInput.next('.invalid-feedback').length === 0) {
                                    fotoInput.after(`
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
                    } else {
                        audio.play();
                        toastr.error("Terjadi kesalahan!", "GAGAL!", {
                            progressBar: true,
                            timeOut: 1500,
                            positionClass: "toast-bottom-right",
                        });
                    }

                    spinner.addClass('d-none');
                    btnText.text('Simpan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
