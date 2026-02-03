<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
    @endphp


    @php
        $konfigurasi = \App\Models\PengaturanProfil::first();
        $icon = \App\Models\PengaturanMedia::where('jenis_data', 'fav icon')->first();
        $faviconPath =
            $icon && $icon->nama_file ? asset('config_media/' . $icon->nama_file) : asset('default/favicon.ico');
    @endphp


    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon" />
    <title>{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/dist/css/adminlte.min.css') }}" />

    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/toastr/toastr.min.css') }}">

    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.416);
        }

        body>* {
            position: relative;
            z-index: 1;
        }

        .login-container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            max-width: 900px;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
        }

        .login-image {
            flex: 1;
            min-height: 500px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-image img {
            max-width: 80%;
            height: auto;
        }

        .login-form {
            flex: 1;
            background-color: #6610f2;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 0 20px 20px 0;
        }

        .form-control-icon {
            position: relative;
        }

        .form-control-icon i {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 13px;
            pointer-events: none;
        }

        .form-control-icon input.form-control {
            padding-left: 2.5rem;
        }

        .btn-login {
            background-color: #333333;
            color: white;
            font-weight: bold;
            border-radius: 50rem;
            transition: background 0.3s ease;
            border: none;
        }

        .btn-login:hover {
            background-color: #222222;
            color: white;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                border-radius: 20px;
            }

            .login-form {
                border-radius: 0 0 20px 20px;
            }

            .login-image {
                border-radius: 20px 20px 0 0;
                min-height: 200px;
                padding-top: 1rem;
                padding-bottom: 1rem;
            }

            .login-image img {
                max-width: 50%;
                margin-top: -10px;
            }
        }

        .form-control-icon i {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%) scale(1);
            color: #6c757d;
            font-size: 13px;
            pointer-events: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .form-control-icon:focus-within i {
            color: ##6610f2 transform: translateY(-50%) scale(1.3);
        }

        .bold-text {
            font-weight: bold;
        }
    </style>
</head>

<body
    style="
    margin: 0; padding: 0;
    background-color: #455a64;
    background-image: radial-gradient(#1c1c1c33 2px, transparent 2px);
    background-size: 20px 20px;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;">


    <div class="login-container">
        <div class="login-image">
            <img src="{{ asset('assets/img/iconLogin.svg') }}" alt="Ilustrasi" />
        </div>

        <div class="login-form text-white">
            <div class="text-center mb-3">
                <img src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" alt="Logo"
                    style="max-width: 80px; height: auto;" class="mb-2" />
                <h5 class="font-weight-bold text-white">
                    {{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}
                </h5>
            </div>

            <form id="formLogin">
                @csrf
                <div class="form-group mb-3 form-control-icon">
                    <i class="fas fa-user fa-sm"></i>
                    <input type="text" style="border-radius: 50px;"
                        class="form-control form-control-lg bold-text w-100" name="username" id="username"
                        placeholder="Username">
                </div>

                <div class="form-group mb-3 form-control-icon">
                    <i class="fas fa-lock fa-sm"></i>
                    <input type="password" style="border-radius: 50px;"
                        class="form-control form-control-lg bold-text w-100" name="password" id="password"
                        placeholder="Password">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-md w-100 btn-login" id="submitBtn">
                        <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                            aria-hidden="true"></span>
                        <span class="button-text">Login</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('templates/dist/js/adminlte.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('templates/plugins/toastr/toastr.min.js') }}"></script>
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $(document).ready(function() {
            function refreshCsrfToken(callback) {
                $.get('{{ route('refresh.csrf') }}', function(data) {
                    $('meta[name="csrf-token"]').attr('content', data.token);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': data.token
                        }
                    });
                    if (typeof callback === 'function') callback();
                });
            }

            $('#formLogin').on('submit', function(e) {
                e.preventDefault();

                let form = this;

                refreshCsrfToken(function() {
                    let url = '{{ route('admin.loginPost') }}';
                    let formData = new FormData(form);

                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();

                    let submitBtn = $('#submitBtn');
                    let spinner = submitBtn.find('.spinner-border');
                    let btnText = submitBtn.find('.button-text');

                    spinner.removeClass('d-none');
                    btnText.text('Masuk...');
                    submitBtn.prop('disabled', true);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function() {
                            window.location.href = "{{ route('dashboard.index') }}";
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                audio.play();
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, val) {
                                    let input = $('#' + key);
                                    input.addClass('is-invalid');
                                    input.after(
                                        '<span class="invalid-feedback" role="alert"><strong>' +
                                        val[0] + '</strong></span>'
                                    );
                                });
                            } else {
                                alert(
                                    'Terjadi kesalahan pada server. Silakan coba lagi.'
                                );
                            }
                        },
                        complete: function() {
                            spinner.addClass('d-none');
                            btnText.text('Login');
                            submitBtn.prop('disabled', false);
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
