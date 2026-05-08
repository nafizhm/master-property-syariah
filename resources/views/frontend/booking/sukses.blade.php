@extends('layouts.app')

@section('title', 'Booking Berhasil - Taman Jivva Kemlaten')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
            background:
                linear-gradient(rgba(8, 37, 19, 0.72), rgba(8, 37, 19, 0.72)),
                url('{{ asset('config_media/booking-bg.png') }}') no-repeat center center fixed !important;
            background-size: cover !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .booking-container {
            width: 100%;
            max-width: 550px;
            padding: 20px;
        }

        .booking-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.98);
            animation: fadeInUp 0.6s ease-out;
        }

        .booking-header {
            background: linear-gradient(135deg, #0d5c2e 0%, #1a7a42 50%, #228B4a 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .success-icon-wrapper {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both 0.3s;
        }

        .success-icon-wrapper i {
            font-size: 35px;
            color: #1a7a42;
        }

        .booking-header h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
        }

        .booking-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 8px 0 0;
            font-size: 0.9rem;
        }

        .booking-body {
            padding: 40px 35px;
        }

        .detail-card {
            background: #f8faf9;
            border: 1.5px solid #e0efe5;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #e0efe5;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .detail-value {
            color: #1a5c30;
            font-weight: 700;
            font-size: 0.95rem;
            text-align: right;
        }

        .info-alert {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 30px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .info-alert i {
            color: #16a34a;
            margin-top: 3px;
        }

        .info-alert p {
            margin: 0;
            font-size: 0.85rem;
            color: #166534;
            line-height: 1.5;
        }

        .btn-home {
            background: linear-gradient(135deg, #1a7a42, #2ea55a);
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(26, 122, 66, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 122, 66, 0.4);
            filter: brightness(1.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 576px) {
            .booking-body {
                padding: 30px 20px;
            }

            .booking-header {
                padding: 30px 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="booking-container">
        <div class="booking-card">
            <div class="booking-header">
                <div class="success-icon-wrapper">
                    <i class="fas fa-check"></i>
                </div>
                <h4>Booking Berhasil!</h4>
                <p>Terima kasih atas kepercayaan Anda memilih hunian kami.</p>
            </div>

            <div class="booking-body">
                @php
                    $nama = session('nama', '-');
                    $lokasi = session('lokasi', '-');
                    $blok = session('blok', '-');
                @endphp

                <div class="detail-card">
                    <div class="detail-item">
                        <span class="detail-label">Nama Pemesan</span>
                        <span class="detail-value text-uppercase">{{ $nama }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lokasi Perumahan</span>
                        <span class="detail-value">{{ $lokasi }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Unit/Blok Kavling</span>
                        <span class="detail-value">{{ $blok }}</span>
                    </div>
                </div>

                <div class="info-alert">
                    <i class="fas fa-info-circle"></i>
                    <p>Permohonan booking Anda telah kami terima. Tim admin KPR kami akan segera menghubungi Anda untuk
                        proses selanjutnya.</p>
                </div>

                <div class="text-center">
                    <a href="{{ route('booking') }}" class="btn-home">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Form Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        $(document).ready(function() {
            // Celebrate success!
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = {
                startVelocity: 30,
                spread: 360,
                ticks: 60,
                zIndex: 0
            };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, {
                    particleCount,
                    origin: {
                        x: randomInRange(0.1, 0.3),
                        y: Math.random() - 0.2
                    }
                }));
                confetti(Object.assign({}, defaults, {
                    particleCount,
                    origin: {
                        x: randomInRange(0.7, 0.9),
                        y: Math.random() - 0.2
                    }
                }));
            }, 250);
        });
    </script>
@endpush
