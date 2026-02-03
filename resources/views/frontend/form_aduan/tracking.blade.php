@extends('layouts.app')
@section('title', 'Form')

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
                <strong>Form Tracking Aduan</strong>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tracking.form') }}">
                    @csrf
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label fw-bold">Nomor Aduan<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" name="no_aduan" class="form-control" value="{{ request('no_aduan') }}"
                                required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Tracking Aduan</button>
                    </div>
                </form>

                @if ($allProses && count($allProses))
                    <div class="mt-5">
                        <h5 class="text-center mb-3">Riwayat Status Aduan</h5>

                        @foreach ($allProses as $item)
                            <div class="d-flex align-items-start mb-3">
                                <div style="border-left: 3px solid #ccc; height: 60px; margin-right: 15px;"></div>

                                <div class="flex-grow-1">
                                    <div class="text-muted mb-1">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($item->tgl_update)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }}
                                    </div>
                                    <div class="fw-bold text-success">
                                        <i class="fas fa-check-circle"></i>
                                        {{ $statusList[$item->stt_proses_aduan] ?? 'Status Tidak Diketahui' }}
                                    </div>
                                    @if ($item->catatan)
                                        <div class="text-secondary mt-1">
                                            <i class="fas fa-comment-dots"></i> {{ $item->catatan }}
                                        </div>
                                    @endif

                                    <!-- Preview Foto untuk Status Proses Selesai -->
                                    @if ($item->stt_proses_aduan == 3)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="loadFotoProses({{ $aduan->id }})">
                                                <i class="fas fa-camera"></i> Lihat Foto Hasil Pengerjaan
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal untuk Preview Foto -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">
                        <i class="fas fa-camera"></i> Foto Hasil Pengerjaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="fotoContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat foto...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function loadFotoProses(aduanId) {
            $('#fotoContent').html(`
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <p class="mt-2">Memuat foto...</p>
                </div>
            `);

            $('#fotoModal').modal('show');

            $.ajax({
                url: `{{ route('getFoto.prosesAduan', ':id') }}`.replace(':id', aduanId),
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        let fotoHtml = '<div class="row">';

                        response.data.forEach(function(foto, index) {
                            fotoHtml += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <img src="${foto.url}" class="card-img-top" alt="Foto ${index + 1}" 
                                     style="height: 200px; object-fit: cover; cursor: pointer;"
                                     onclick="showFullImage('${foto.url}')">
                                <div class="card-body p-2">
                                    <small class="text-muted">Foto ${index + 1}</small>
                                </div>
                            </div>
                        </div>
                    `;
                        });

                        fotoHtml += '</div>';
                        $('#fotoContent').html(fotoHtml);
                    } else {
                        $('#fotoContent').html(`
                    <div class="text-center text-muted">
                        <i class="fas fa-image fa-3x mb-3"></i>
                        <p>Tidak ada foto tersedia untuk aduan ini.</p>
                    </div>
                `);
                    }
                },
                error: function(xhr, status, error) {
                    $('#fotoContent').html(`
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p>Terjadi kesalahan saat memuat foto.</p>
                    <small>Error: ${error}</small>
                </div>
            `);
                }
            });
        }

        function showFullImage(imageUrl) {
            // Create full image modal
            const fullImageModal = `
        <div class="modal fade" id="fullImageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preview Foto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageUrl}" class="img-fluid" alt="Full Image">
                    </div>
                    <div class="modal-footer">
                        <a href="${imageUrl}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Buka di Tab Baru
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Remove existing full image modal if any
            $('#fullImageModal').remove();

            // Add and show new modal
            $('body').append(fullImageModal);
            $('#fullImageModal').modal('show');

            // Clean up when modal is hidden
            $('#fullImageModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    </script>
@endpush