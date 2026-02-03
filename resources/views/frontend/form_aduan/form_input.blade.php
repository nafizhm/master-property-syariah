@extends('layouts.app')

@push('styles')
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

@section('title', 'Form Aduan')

@section('content')
    <div class="container mt-4" style="max-width: 600px;">

        @php
            $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
        @endphp

        <!-- Logo GESYA -->
        <div class="text-center mb-4">
            <img src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" alt="Logo Website" width="200">
        </div>

        <!-- Card Form -->
        <div class="card shadow">
            <div class="card-header bg-white">
                <strong>Form Aduan / Komplain</strong>
            </div>
            <div class="card-body">
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    @if (isset($customer) && isset($kavling))
                        <input type="hidden" name="id_customer" value="{{ $customer->id }}">
                        <input type="hidden" name="id_kavling" value="{{ $kavling->id }}">
                    @endif

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label fw-bold">Nomor Kontrak <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" name="no_kontrak" class="form-control" id="no_kontrak"
                                value="{{ isset($customer) ? $customer->kode_customer ?? '' : '' }}"
                                {{ isset($customer) ? 'readonly' : '' }} required>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label fw-bold">Nomor Telepon <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" name="no_telepon" class="form-control" id="no_telepon"
                                value="{{ isset($customer) ? $customer->no_telp ?? '' : '' }}"
                                {{ isset($customer) ? 'readonly' : '' }} required>
                        </div>
                    </div>

                    <div class="text-end" id="btn-wrapper">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-lanjut">Lanjutkan</button>
                        <a href="{{ route('tracking.form') }}" class="btn btn-warning btn-sm text-dark"
                            id="btn-tracking">Tracking Aduan</a>
                    </div>

                    <div id="form-lanjutan" class="mt-4" style="display: none;">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input type="text" name="nama_lengkap" class="form-control" id="nama_lengkap"
                                    value="{{ isset($customer) ? $customer->nama_lengkap ?? '' : '' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Lokasi Perumahan</label>
                            <div class="col-sm-8">
                                <input type="text" name="lokasi" class="form-control" id="lokasi"
                                    value="{{ isset($kavling) ? $kavling->lokasi->nama_kavling ?? '' : '' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Blok / Unit</label>
                            <div class="col-sm-8">
                                <input type="text" name="blok_unit" class="form-control" id="blok_unit"
                                    value="{{ isset($kavling) ? $kavling->kode_kavling ?? '' : '' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Isi Aduan <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <textarea name="isi_aduan" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label fw-bold" for="lampiran">Foto Kondisi</label>
                            <div class="col-sm-8">
                                <input type="file" class="form-control-file" id="lampiran" name="lampiran[]" multiple
                                    accept=".jpg,.jpeg,.png" onchange="previewFoto(this)">
                                <small class="form-text text-muted">
                                    Format: JPG, PNG, GIF. Maksimal 2MB
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Preview Foto</label>
                            <div id="preview-container" class="d-flex flex-wrap gap-2 position-relative">
                                <div id="no-preview"
                                    class="preview-foto d-flex align-items-center justify-content-center text-muted"
                                    style="width: 150px; height: 150px; border: 1px dashed #ccc;">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="button-text">Kirim Aduan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const hasCustomerData = {{ isset($customer) ? 'true' : 'false' }};

        document.getElementById('btn-lanjut').addEventListener('click', function() {
            if (!hasCustomerData) {
                const noKontrak = document.getElementById('no_kontrak').value;
                const noTelepon = document.getElementById('no_telepon').value;

                if (!noKontrak.trim() || !noTelepon.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Nomor kontrak dan nomor telepon harus diisi!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                fetchCustomerData(noKontrak, noTelepon);
            } else {
                document.getElementById('form-lanjutan').style.display = 'block';
                document.getElementById('btn-wrapper').style.display = 'none';
            }
        });

        function fetchCustomerData(noKontrak, noTelepon) {
            let btnLanjut = document.getElementById('btn-lanjut');
            btnLanjut.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memuat...';
            btnLanjut.disabled = true;

            $.ajax({
                url: '{{ route('serah-terima-kunci.fetch-customer') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    no_kontrak: noKontrak,
                    no_telepon: noTelepon
                },
                success: function(response) {
                    document.getElementById('nama_lengkap').value = response.nama_lengkap || '';
                    document.getElementById('lokasi').value = response.lokasi || '';
                    document.getElementById('blok_unit').value = response.blok_unit || '';

                    if (response.id_customer) {
                        let hiddenCustomer = document.createElement('input');
                        hiddenCustomer.type = 'hidden';
                        hiddenCustomer.name = 'id_customer';
                        hiddenCustomer.value = response.id_customer;
                        document.getElementById('formData').appendChild(hiddenCustomer);
                    }

                    if (response.id_kavling) {
                        let hiddenKavling = document.createElement('input');
                        hiddenKavling.type = 'hidden';
                        hiddenKavling.name = 'id_kavling';
                        hiddenKavling.value = response.id_kavling;
                        document.getElementById('formData').appendChild(hiddenKavling);
                    }

                    document.getElementById('no_kontrak').readOnly = true;
                    document.getElementById('no_telepon').readOnly = true;

                    document.getElementById('form-lanjutan').style.display = 'block';
                    document.getElementById('btn-wrapper').style.display = 'none';
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat memuat data!';

                    if (xhr.status === 410 && xhr.responseJSON && xhr.responseJSON.expired) {
                        window.location.href = '{{ route('form-aduan.expired') }}';
                        return;
                    } else if (xhr.status === 404) {
                        errorMessage =
                            'Data customer tidak ditemukan! Periksa kembali nomor kontrak dan telepon.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                        timer: 3000,
                        showConfirmButton: false
                    });

                    btnLanjut.innerHTML = 'Lanjutkan';
                    btnLanjut.disabled = false;
                }
            });
        }

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

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

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Mengirim...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('serah-terima-kunci.submit') }}';
            let method = 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this);
            selectedFiles.forEach((file, index) => {
                formData.append(`lampiran[]`, file);
            });
            formData.append('_method', method);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    audio.play();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data berhasil dikirim!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href =
                            `/form-aduan/sukses?no_aduan=${response.no_aduan}&nama=${encodeURIComponent(response.nama)}&lokasi=${encodeURIComponent(response.lokasi)}&blok=${encodeURIComponent(response.blok)}`;
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        audio.play();
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL!',
                            text: 'Ada inputan yang salah!',
                            timer: 3500,
                            timerProgressBar: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            toast: true
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
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL!',
                            text: 'Terjadi kesalahan pada sistem!',
                            timer: 3500,
                            timerProgressBar: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            toast: true
                        });
                    }

                    spinner.addClass('d-none');
                    btnText.text('Kirim Aduan');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
