@extends('layouts.app')

@section('title', 'Booking Sentosa Era Wijaya')

@section('content')
    <div class="container">
        @php
            $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
        @endphp

        <center>
            <img class="mt-5 mb-5" src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}"
                style="max-width: 150px; height: auto;">
        </center>

        <section class="content">
            <div class="card">
                <div class="card-header bg-indigo">
                    <h4 class="fw-bold">Form Data Customer (Booking)</h4>
                </div>
                <div class="card-body">
                    <form id="formData" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal</label>
                            <div class="col-sm-2">
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Nama Lengkap <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <input name="nama_lengkap" id="nama_lengkap" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">NIK <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <input name="nik" id="nik" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Tempat Lahir <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <input name="tempat_lahir" id="tempat_lahir" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">Tanggal Lahir <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <input name="tgl_lahir" id="tgl_lahir" class="form-control" type="date">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">No. Telp / WA <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <input name="no_telp" id="no_telp" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">Jenis Kelamin <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-control select-jk" name="jenis_kelamin" id="jenis_kelamin">
                                    <option value=""></option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Email</label>
                            <div class="col-sm-4">
                                <input name="email" id="email" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">NPWP</label>
                            <div class="col-sm-3">
                                <input name="npwp" id="npwp" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Pekerjaan</label>
                            <div class="col-sm-4">
                                <input name="pekerjaan" id="pekerjaan" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">No. BPJS Kes</label>
                            <div class="col-sm-3">
                                <input name="no_bpjs_kes" id="no_bpjs_kes" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Alamat KTP <span style="color: red;">*</span></label>
                            <div class="col-sm-6">
                                <textarea name="alamat_ktp" id="alamat_ktp" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3">Alamat Domisili <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-6">
                                <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Status Pernikahan</label>
                            <div class="col-sm-4">
                                <select class="form-control select-status" name="status_pernikahan"
                                    id="status_pernikahan">
                                    <option value=""></option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                    <option value="Menikah">Menikah</option>
                                </select>
                            </div>
                        </div>

                        <div id="pasangan">
                            <div class="form-group row">
                                <label class="control-label col-sm-3">Nama Pasangan</label>
                                <div class="col-sm-4">
                                    <input name="nama_p" id="nama_p" class="form-control" type="text">
                                </div>
                                <label class="control-label col-sm-2">NIK Pasangan</label>
                                <div class="col-sm-3">
                                    <input name="nik_p" id="nik_p" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Nama Saudara</label>
                            <div class="col-sm-4">
                                <input name="nama_saudara" id="nama_saudara" class="form-control" type="text">
                            </div>
                            <label class="control-label col-sm-2">No. Telp Saudara</label>
                            <div class="col-sm-3">
                                <input name="no_telp_saudara" id="no_telp_saudara" class="form-control" type="text">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Lokasi Perumahan <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-control select-lokasi" name="id_lokasi" id="id_lokasi">
                                    <option value=""></option>
                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_kavling }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Blok/Kav <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select name="id_kavling" id="id_kavling" class="form-control select-kavling"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Harga Rumah</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="hrg_jual" id="hrg_jual"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Marketing Inhouse<span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-control select-marketing" name="id_marketing" id="id_marketing">
                                    <option value=""></option>
                                    <option value="0">Non Marketing</option>
                                    @foreach ($marketing as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_marketing }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Marketing Agent</label>
                            <div class="col-sm-3">
                                <select class="form-control select-agent" name="id_agent" id="id_agent">
                                    <option value=""></option>
                                    @foreach ($agent as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama_agent }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Jenis Perumahan <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-control select-jp" name="jenis_perumahan" id="jenis_perumahan">
                                    <option value=""></option>
                                    <option value="Subsidi">Subsidi</option>
                                    <option value="Komersil">Komersil</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Jenis Pembelian <span
                                    style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-control select-pembelian" name="jenis_pembelian"
                                    id="jenis_pembelian">
                                    <option value=""></option>
                                    <option value="Pembelian Cash">Pembelian Cash</option>
                                    <option value="Cash Bertahap">Cash Bertahap</option>
                                    <option value="KPR">KPR</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Booking Fee <span style="color: red;">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input name="booking_fee" id="booking_fee" class="form-control format-number"
                                        type="text">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Foto Pemohon</label>
                            <div class="col-sm-4">
                                <input name="foto_pemohon" id="foto_pemohon" type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="handleFileChange(this, 'preview_foto_pemohon')">
                                <div id="preview_foto_pemohon" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_pemohon', 'preview_foto_pemohon')">Hapus</button>
                                </div>
                            </div>
                            <label class="control-label col-sm-2">Foto KTP <span style="color: red;">*</span></label>
                            <div class="col-sm-3">
                                <input name="foto_ktp" id="foto_ktp" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="handleFileChange(this, 'preview_foto_ktp')">
                                <div id="preview_foto_ktp" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_ktp', 'preview_foto_ktp')">Hapus</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3">Foto NPWP</label>
                            <div class="col-sm-4">
                                <input name="foto_npwp" id="foto_npwp" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="handleFileChange(this, 'preview_foto_npwp')">
                                <div id="preview_foto_npwp" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_npwp', 'preview_foto_npwp')">Hapus</button>
                                </div>
                            </div>
                            <label class="control-label col-sm-2">Foto KK</label>
                            <div class="col-sm-3">
                                <input name="foto_kk" id="foto_kk" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="handleFileChange(this, 'preview_foto_kk')">
                                <div id="preview_foto_kk" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_kk', 'preview_foto_kk')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Foto BPJS</label>
                            <div class="col-sm-4">
                                <input name="foto_bpjs" id="foto_bpjs" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="handleFileChange(this, 'preview_foto_bpjs')">
                                <div id="preview_foto_bpjs" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_bpjs', 'preview_foto_bpjs')">Hapus</button>
                                </div>
                            </div>
                            <label class="control-label col-sm-2">Foto KTP Pasangan</label>
                            <div class="col-sm-3">
                                <input name="foto_ktp_p" id="foto_ktp_p" type="file" accept=".jpg,.jpeg,.png,.pdf"
                                    onchange="handleFileChange(this, 'preview_foto_ktp_p')">
                                <div id="preview_foto_ktp_p" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('foto_ktp_p', 'preview_foto_ktp_p')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3">Bukti Transfer</label>
                            <div class="col-sm-4">
                                <input name="file_bukti" id="file_bukti" type="file"
                                    onchange="handleFileChange(this, 'preview_file_bukti')" accept=".jpg,.jpeg,.png,.pdf">
                                <div id="preview_file_bukti" class="mt-2 d-none">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="showPreview(this)">View</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="clearFile('file_bukti', 'preview_file_bukti')">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="button-text font-weight-bold">Kirim Data</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" data-focus="false"
        aria-labelledby="previewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header bg-indigo">
                    <h5 class="modal-title" id="previewModalLabel">Preview File</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">

                    <img id="modalPreviewImage" class="img-fluid d-none" alt="Preview">

                    <iframe id="modalPreviewPdf" class="w-100 d-none" style="height:500px;" frameborder="0">
                    </iframe>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Keluar
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript">
        function formatAngkaRibuan(angka) {
            return angka.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).on('input', '#booking_fee', function() {
            let nilai = $(this).val();
            let terformat = formatAngkaRibuan(nilai);
            $(this).val(terformat);
        });

        function handleFileChange(input, previewId) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function(e) {
                const previewDiv = document.getElementById(previewId);
                const viewBtn = previewDiv.querySelector('button.btn-primary');

                viewBtn.setAttribute('data-src', e.target.result);
                viewBtn.setAttribute('data-type', file.type);

                input.style.display = 'none';
                previewDiv.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        }

        function showPreview(btn) {
            const src = btn.getAttribute('data-src');
            const type = btn.getAttribute('data-type');

            const img = document.getElementById('modalPreviewImage');
            const pdf = document.getElementById('modalPreviewPdf');

            if (type === 'application/pdf') {
                img.classList.add('d-none');
                pdf.classList.remove('d-none');
                pdf.src = src;
            } else {
                pdf.classList.add('d-none');
                img.classList.remove('d-none');
                img.src = src;
            }

            const myModal = new bootstrap.Modal(document.getElementById('previewModal'));
            myModal.show();
        }

        function clearFile(inputId, previewId) {
            const input = document.getElementById(inputId);
            input.value = '';
            input.style.display = 'block';

            document.getElementById(previewId).classList.add('d-none');

            document.getElementById('modalPreviewImage').src = '';
            document.getElementById('modalPreviewPdf').src = '';
        }

        $(document).ready(function() {

            $('.select-lokasi').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Lokasi",
            });
            $('.select-jk').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Kelamin",
            });
            $('.select-kavling').select2({
                theme: "bootstrap4",
            });
            $('.select-marketing').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Marketing",
            });

            $('.select-agent').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Agent",
            });

            $('.select-jp').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Perumahan",
            });

            $('.select-pembelian').select2({
                theme: "bootstrap4",
                minimumResultsForSearch: Infinity,
                placeholder: "Pilih Jenis Pembelian",
            });

            $('.select-status').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
            });
        });

        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi",
            });

            $('.select-kavling').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Kavling",
            });

            const routeGetKavling = "{{ route('booking.getKavling', ':id') }}";
            const routeGetHarga = "{{ route('booking.getHargaKavling', ':id') }}";

            $('#id_lokasi').on('change', function() {
                let idLokasi = $(this).val();
                $('#id_kavling').html('<option value="">Loading...</option>').trigger('change');
                $('#hrg_jual').val('');

                if (idLokasi) {
                    const urlKavling = routeGetKavling.replace(':id', idLokasi);
                    $.get(urlKavling, function(data) {
                        let options = '<option value=""></option>';
                        data.forEach(function(item) {
                            options +=
                                `<option value="${item.id}">${item.kode_kavling}</option>`;
                        });
                        $('#id_kavling').html(options).trigger('change');
                    });
                }
            });

            $('#id_kavling').on('change', function() {
                let idKavling = $(this).val();

                if (idKavling) {
                    const urlHarga = routeGetHarga.replace(':id', idKavling);
                    $.get(urlHarga, function(data) {
                        $('#hrg_jual').val(formatRupiah(data.hrg_jual));
                    });
                } else {
                    $('#hrg_jual, #total_harga').val('');
                }
            });

        });

        function formatRupiah(angka) {
            if (!angka) return '';
            return angka.toString().replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $('#status').on('change', function() {
            if ($(this).val() === 'Menikah') {
                $('#pasangan').show();
                $('#nama_p, #nik_p').prop('required', true);
            } else {
                $('#pasangan').hide();
                $('#nama_p, #nik_p').prop('required', false);
            }
        }).trigger('change');

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        $('#formData').on('submit', function(e) {
            e.preventDefault();

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.removeClass('d-none');
            btnText.text('Mengirim...');
            submitBtn.prop('disabled', true);

            let url = '{{ route('store.booking') }}';
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
                success: function() {
                    sessionStorage.setItem('success', 'Booking berhasil dikirimkan.');
                    spinner.addClass('d-none');
                    btnText.text('Kirim Data');
                    submitBtn.prop('disabled', false);
                    window.location.href = "{{ route('booking.sukses') }}";
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
                            if (key.includes('.')) {
                                let parts = key.split('.');
                                let field = parts[0];
                                let index = parseInt(parts[1]);

                                let inputSelector;

                                if ($(`[name="${field}[]"]`).length > 0) {
                                    inputSelector = $(`[name="${field}[]"]`).eq(index);
                                } else {
                                    return;
                                }

                                inputSelector.addClass('is-invalid');
                                inputSelector.closest('.form-control, .form-select').parent()
                                    .find('.invalid-feedback').remove();
                                inputSelector.closest('.form-control, .form-select').parent()
                                    .append(
                                        `<span class="invalid-feedback" role="alert"><strong>${val[0]}</strong></span>`
                                    );
                            } else {
                                let input = $('#' + key);
                                input.addClass('is-invalid');
                                input.parent().find('.invalid-feedback').remove();
                                input.parent().append(
                                    '<span class="invalid-feedback" role="alert"><strong>' +
                                    val[0] + '</strong></span>'
                                );
                            }
                        });
                    }
                    spinner.addClass('d-none');
                    btnText.text('Kirim Data');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
