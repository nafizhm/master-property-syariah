@extends('layouts.app')

@push('styles')
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }

        .card {
            transition: all 0.3s ease;
            border-radius: 16px;
        }

        .info-box {
            transition: transform 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-box:hover {
            transform: translateY(-2px);
        }

        .btn {
            transition: all 0.3s ease;
            border-width: 2px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert {
            border-left: 4px solid #dc3545;
        }

        .card-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .card-body {
                padding: 2rem 1.5rem !important;
            }

            .btn {
                font-size: 0.9rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease;
        }

        .fas {
            transition: transform 0.3s ease;
        }

        .btn:hover .fas {
            transform: scale(1.1);
        }
    </style>
@endpush

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                @php
                    $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
                @endphp

                {{-- Logo Section --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" alt="Logo Website"
                        class="img-fluid mb-3" style="max-width: 120px;">
                </div>

                {{-- Main Card --}}
                <div class="card shadow-lg border-0 overflow-hidden">
                    <div class="card-header bg-gradient text-white text-center py-4"
                        style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                        <div class="mb-3">
                            <i class="fas fa-clock fa-4x mb-3 opacity-75"></i>
                        </div>
                        <h2 class="mb-2 fw-bold">Link Sudah Expired</h2>
                        <p class="mb-0 opacity-90">Masa berlaku formulir pengaduan telah berakhir</p>
                    </div>

                    <div class="card-body p-5">
                        {{-- Alert Message --}}
                        <div class="alert alert-danger border-0 mb-4" role="alert"
                            style="background-color: #f8d7da; border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-2 text-danger">Formulir Telah Kedaluwarsa</h5>
                                    <p class="mb-0 text-dark">
                                        Maaf, masa berlaku formulir pengaduan ini telah berakhir.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Information Section --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box p-3 rounded" style="background-color: #f8f9fa;">
                                    <h6 class="text-primary mb-2 mr-3">
                                        <i class="fas fa-info-circle mr-2"></i>Informasi
                                    </h6>
                                    <ul class="list-unstyled mb-0 small text-muted">
                                        <li><i class="fas fa-check text-success me-2"></i>Link telah kedaluwarsa</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Data tetap aman tersimpan</li>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="info-box p-3 rounded" style="background-color: #fff3cd;">
                                    <h6 class="text-warning mb-2 mr-3">
                                        <i class="fas fa-lightbulb mr-2"></i>Tambahan
                                    </h6>
                                    <ul class="list-unstyled mb-0 small text-muted">
                                        <li><i class="fas fa-phone text-primary me-2"></i>Hubungi customer service</li>
                                        <li><i class="fas fa-envelope text-primary me-2"></i>Kirim email ke admin</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Information --}}
                        <div class="text-center mb-4">
                            <h5 class="text-dark mb-3">
                                <i class="fas fa-headset me-2 text-primary"></i>
                                Butuh Bantuan?
                            </h5>
                            <p class="text-muted mb-3">
                                Silakan hubungi tim customer service kami untuk mendapatkan bantuan lebih lanjut
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="tel:+62xxx" class="btn btn-outline-primary w-100 py-3 rounded-3">
                                    <i class="fas fa-phone mb-2 d-block"></i>
                                    <span class="fw-semibold">Telepon</span><br>
                                    <small class="opacity-75">Customer Service</small>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="mailto:admin@gesya.com" class="btn btn-outline-success w-100 py-3 rounded-3">
                                    <i class="fas fa-envelope mb-2 d-block"></i>
                                    <span class="fw-semibold">Email</span><br>
                                    <small class="opacity-75">admin@gesya.com</small>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="https://wa.me/62xxx" target="_blank"
                                    class="btn btn-outline-info w-100 py-3 rounded-3">
                                    <i class="fab fa-whatsapp mb-2 d-block"></i>
                                    <span class="fw-semibold">WhatsApp</span><br>
                                    <small class="opacity-75">Chat Admin</small>
                                </a>
                            </div>
                        </div>

                        {{-- Additional Actions --}}
                        <div class="text-center mt-4 pt-3 border-top">
                            <a href="{{ route('tracking.form') }}" class="btn btn-primary me-3 px-4 py-2">
                                <i class="fas fa-search me-2"></i>Cek Status Aduan
                            </a>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="card-footer bg-light text-center py-3 border-0">
                        <div class="d-flex align-items-center justify-content-center text-muted">
                            <img src="{{ asset('assets/img/logo_gesya.png') }}" width="24" height="24" class="me-2"
                                alt="Logo">
                            <strong>ADMIN GESYA GROUP</strong>
                        </div>
                        <small class="text-muted d-block mt-1">
                            &copy; {{ date('Y') }} Gesya Group. Melayani dengan sepenuh hati.
                        </small>
                    </div>
                </div>

                {{-- Timeline Info --}}
                <div class="card mt-4 border-0 bg-transparent">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-clock me-2"></i>Waktu Akses Link
                        </h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-3 rounded bg-light">
                                    <i class="fas fa-calendar-check text-success fa-2x mb-2"></i>
                                    <div class="small">
                                        <strong>Diberikan</strong><br>
                                        <span class="text-muted">Link aktif</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded bg-light">
                                    <i class="fas fa-hourglass-half text-warning fa-2x mb-2"></i>
                                    <div class="small">
                                        <strong>Berlaku</strong><br>
                                        <span class="text-muted">Periode tertentu</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded bg-light">
                                    <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>
                                    <div class="small">
                                        <strong>Expired</strong><br>
                                        <span class="text-muted">Tidak dapat diakses</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
