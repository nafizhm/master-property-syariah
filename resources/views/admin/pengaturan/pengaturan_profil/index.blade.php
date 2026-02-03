<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 32px;
}

.toggle-switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 34px;
    transition: 0.4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: 0.4s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    font-size: 14px;
    color: #28a745;
}

.toggle-switch input:not(:checked) + .slider:before {
    content: "\f00d";
    color: #dc3545;
}

.toggle-switch input:checked + .slider:before {
    transform: translateX(28px);
    content: "\f00c";
    color: #28a745;
}

.toggle-switch input:checked + .slider {
    background-color: #28a74544;
}

.toggle-switch input:not(:checked) + .slider {
    background-color: #dc354544;
}
</style>


@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
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
                                    <h3 class="font-weight-bold text-lg">Pengaturan Aplikasi</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('pengaturan-profil.update', $data->id) }}" method="POST" id="konfigurasi-form">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="id" value="{{ $data->id }}">

                                    <div class="form-group row mb-3">
                                        <label for="nama_perusahaan" class="col-sm-3 col-form-label">Nama Perusahaan</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                                id="nama_perusahaan" name="nama_perusahaan" placeholder="Nama Perusahaan"
                                                value="{{ old('nama_perusahaan', $data->nama_perusahaan) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                            @error('nama_perusahaan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" placeholder="Alamat"
                                                rows="2" @if ($permissions['edit'] == 0) readonly @endif>{{ old('alamat', $data->alamat) }}</textarea>
                                            @error('alamat')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" placeholder="Email"
                                                value="{{ old('email', $data->email) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="telp" class="col-sm-3 col-form-label">No. Telp</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control @error('telp') is-invalid @enderror"
                                                id="telp" name="telp" placeholder="Telepon"
                                                value="{{ old('telp', $data->telp) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                            @error('telp')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="hape" class="col-sm-3 col-form-label">No. HP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control @error('hape') is-invalid @enderror"
                                                id="hape" name="hape" placeholder="Nomor Handphone"
                                                value="{{ old('hape', $data->hape) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                            @error('hape')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-5">
                                        <label for="npwp_perusahaan" class="col-sm-3 col-form-label">NPWP</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                class="form-control @error('npwp_perusahaan') is-invalid @enderror"
                                                id="npwp_perusahaan" name="npwp_perusahaan" placeholder="NPWP Perusahaan"
                                                value="{{ old('npwp_perusahaan', $data->npwp_perusahaan) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                            @error('npwp_perusahaan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="front_page" class="col-sm-3 col-form-label">Front Page</label>
                                        <div class="col-sm-9">
                                          <label class="toggle-switch">
                                            <input type="hidden" name="front_page" value="0">
                                            <input type="checkbox" id="front_page" name="front_page" value="1"
                                                {{ old('front_page', $data->front_page) == 1 ? 'checked' : '' }}
                                                @if ($permissions['edit'] == 0) disabled @endif>
                                            <span class="slider"></span>
                                        </label>
                                        </div>
                                    </div>



                                    <div class="form-group row mb-3">
                                        <label for="folderSVG" class="col-sm-3 col-form-label">Folder SVG
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                class="form-control @error('folder_svg') is-invalid @enderror"
                                                id="folderSVG" name="folder_svg" placeholder="Folder SVG"
                                                value="{{ old('folder_svg', $data->folder_svg) }}"
                                                @if ($permissions['edit'] == 0) readonly @endif>
                                            @error('folder_svg')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    @if ($permissions['edit'] == 1)
                                        <div class="modal-footer justify-content-between">
                                            <button type="submit" class="btn btn-success ml-auto" id="submit-btn">SIMPAN
                                                PENGATURAN</button>
                                        </div>
                                    @endif
                                </form>
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
        <!-- /.content -->
    </div>
    <style>
        input[readonly],
        textarea[readonly] {
            background-color: white !important;
            color: #495057;
            /* Warna teks agar tetap terlihat jelas */
        }
    </style>
@endsection
@push('scripts')
    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        @if (session('success'))
            audio.play();
            toastr.success("{{ session('success') }}", "BERHASIL", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });
        @elseif (session('error'))
            audio.play()
            toastr.error("{{ session('error') }}", "GAGAL!", {
                progressBar: true,
                timeOut: 3500,
                positionClass: "toast-bottom-right",
            });

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
        @endif

        document.getElementById('submit-btn').addEventListener('click', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data akan di update!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Simpan</span>',
                cancelButtonText: 'Tidak, Batalkan',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML = `
                    <span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span>
                    Simpan...`;
                        confirmBtn.disabled = true;

                        document.getElementById('konfigurasi-form').submit();
                    });
                }
            });
        });
    </script>
@endpush
