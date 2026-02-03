@extends('admin.layout_admin')
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
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Data Proyek Saluran</h3>
                                    <div class="d-flex align-items-center">
                                        @if ($permissions['tambah'])
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#modalForm">
                                                <i class="fas fa-plus"></i> Tambah Proyek Saluran
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="50px">No</th>
                                            <th>Tanggal</th>
                                            <th>Proyek</th>
                                            <th>Saluran</th>
                                            <th>Progres</th>
                                            <th>Jenis Pembayaran</th>
                                            <th width="200px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false"
            aria-labelledby="modalFormLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-indigo">
                        <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel"></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formData">
                        @csrf
                        <input type="hidden" id="primary_id" name="primary_id">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">No. Kontrak</label>
                                <div class="col-sm-4">
                                    <input type="text" name="no_kontrak" id="no_kontrak" class="form-control">
                                </div>
                                <label class="col-sm-2 col-form-label">Tanggal</label>
                                <div class="col-sm-3">
                                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Proyek</label>
                                <div class="col-sm-4">
                                    <input type="text" name="nama_proyek" id="nama_proyek" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Pemborong</label>
                                <div class="col-sm-4">
                                    <input type="text" name="nama_pemborong" id="nama_pemborong" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Lokasi Proyek</label>
                                <div class="col-sm-4">
                                    <select name="id_lokasi" id="id_lokasi" class="form-control select-lokasi">
                                        <option value=""></option>
                                        @foreach ($lokasiList as $lokasi)
                                            <option value="{{ $lokasi->id_lokasi }}">{{ $lokasi->nama_kavling }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-sm-2 col-form-label">Saluran</label>
                                <div class="col-sm-3">
                                    <select name="id_saluran" id="id_saluran" class="form-control select-saluran">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Panjang</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <input type="text" name="panjang" id="panjang"
                                            class="form-control format-number" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">m</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Harga Satuan</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" name="harga_satuan" id="harga_satuan"
                                            class="form-control format-number">
                                        <div class="input-group-append">
                                            <span class="input-group-text">/m²</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nilai Pekerjaan</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" name="nilai_pekerjaan" id="nilai_pekerjaan"
                                            class="form-control format-number" readonly>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Jenis Pembayaran</label>
                                <div class="col-sm-4">
                                    <select name="id_bayar" id="id_bayar" class="form-control select-pembayaran">
                                        <option value=""></option>
                                        <option value="1">Persentase</option>
                                        <option value="2">Termin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" id="jumlahTerminRow" style="display: none;">
                                <label class="col-sm-3 col-form-label">Jumlah Termin</label>
                                <div class="col-sm-3">
                                    <input type="text" name="jumlah_termin" id="jumlah_termin" class="form-control">
                                </div>
                            </div>

                            <div id="terminList"></div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary ms-1" id="submitBtn">
                                <span class="spinner-border spinner-border-sm mx-1 d-none" role="status"
                                    aria-hidden="true"></span>
                                <span class="button-text">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        $(document).on('click', '[data-target="#modalForm"]', function() {
            $('#modalFormLabel').text('Tambah Proyek Jalan');
        });

        $(document).ready(function() {
            $('.select-lokasi').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Lokasi Proyek",
            });
            $('.select-saluran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Saluran",
            });
            $('.select-pembayaran').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Jenis Pembayaran",
                minimumResultsForSearch: Infinity
            });
        });

        $('#id_lokasi').on('change', function() {
            let id = $(this).val();
            $('#id_saluran').empty().append('<option value=""></option>');

            if (id) {
                $.get('{{ url('get-saluran-by-lokasi') }}/' + id, function(data) {
                    $.each(data, function(i, val) {
                        $('#id_saluran').append('<option value="' + val.id + '">' + val.nama +
                            '</option>');
                    });
                });
            }

            $('#panjang').val('');
        });

        $('#id_saluran').on('change', function() {
            let id = $(this).val();

            if (id) {
                $.get('{{ url('get-saluran-detail') }}/' + id, function(data) {
                    $('#panjang').val(formatRibuan(data.panjang));
                });
            } else {
                $('#panjang').val('');
            }
        });

        function formatRibuan(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }


        function parseNumber(value) {
            return parseInt(value.replace(/\./g, '')) || 0;
        }

        function formatNumber(value) {
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function updateNilaiPekerjaan() {
            const panjang = parseNumber($('#panjang').val());
            const harga = parseNumber($('#harga_satuan').val());
            const total = panjang * harga;

            $('#nilai_pekerjaan').val(formatNumber(total));
        }

        $(document).on('input', '#panjang, #harga_satuan', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            value = formatNumber(value);
            $(this).val(value);
            updateNilaiPekerjaan();
        });

        $(document).ready(function() {
            $(document).on('input', '.format-number', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                $(this).val(value);
            });
        });

        $(document).ready(function() {
            $('#id_bayar').change(function() {
                const selected = $(this).val();
                if (selected === '2') {
                    $('#jumlahTerminRow').show();
                    $('#terminList').empty();
                } else {
                    $('#jumlahTerminRow').hide();
                    $('#terminList').empty();
                }
            });

            $('#jumlah_termin').on('input', function() {
                const jumlah = parseInt($(this).val());
                const container = $('#terminList');
                container.empty();

                if (jumlah > 0) {
                    for (let i = 1; i <= jumlah; i++) {
                        const row = `
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Termin ${i}</label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" name="termin[]" class="form-control format-number" step="0.01">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                        container.append(row);
                    }
                }
            });
        });

        var audio = new Audio('{{ asset('audio/notification.ogg') }}');
        var permissions = @json($permissions);
        var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

        $(function() {
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                ajax: "{{ route('proyek-saluran.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_kontrak',
                        name: 'no_kontrak',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'saluran',
                        name: 'saluran',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'progres',
                        name: 'progres',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_bayar',
                        name: 'id_bayar',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: showActionColumn,
                        className: 'text-center'
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, ]
            });
        });

        $(document).on('click', '.edit-button', async function() {
            var url = $(this).data('url');

            $.get(url, async function(response) {
                if (response.status === 'success') {
                    $('#modalFormLabel').text('Edit Proyek Saluran');
                    $('#primary_id').val(response.data.id);
                    $('#no_kontrak').val(response.data.no_kontrak);
                    $('#nama_proyek').val(response.data.nama_proyek);
                    $('#nama_pemborong').val(response.data.nama_pemborong);
                    $('#id_lokasi').val(response.data.id_lokasi).trigger('change');

                    await new Promise(resolve => setTimeout(resolve, 300));
                    $('#id_saluran').val(response.data.id_saluran).trigger('change');

                    let harga = parseInt(response.data.harga_satuan).toLocaleString('id-ID');
                    let nilai = parseInt(response.data.nilai_pekerjaan).toLocaleString('id-ID');
                    $('#harga_satuan').val(harga);
                    $('#nilai_pekerjaan').val(nilai);

                    $('#id_bayar').val(response.data.id_bayar).trigger('change');
                    $('#jumlah_termin').val(response.data.jumlah_termin);

                    $('#formData')
                        .find('input, select, textarea')
                        .not('#no_kontrak, #nama_proyek, #nama_pemborong, #tanggal')
                        .prop('disabled', true);

                    await new Promise(resolve => setTimeout(resolve, 500));

                    const container = $('#terminList');
                    container.empty();
                    const jumlah = response.data.jumlah_termin;

                    for (let i = 1; i <= jumlah; i++) {
                        const val = response.termin[i - 1] ?? '';
                        const row = `
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Termin ${i}</label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" class="form-control" value="${val}" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                        container.append(row);
                    }

                    $('#modalForm').modal('show');
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.select-lokasi').val('').trigger('change');
            $('.select-saluran').val('').trigger('change');
            $('.select-pembayaran').val('').trigger('change');
            $('#unit-rumah-wrapper').empty();
            $('#terminList').empty();

            // Enable kembali semua input/select
            $('#formData').find('input, select, textarea').prop('disabled', false);

            let submitBtn = $('#submitBtn');
            let spinner = submitBtn.find('.spinner-border');
            let btnText = submitBtn.find('.button-text');

            spinner.addClass('d-none');
            btnText.text('Simpan');
            submitBtn.prop('disabled', false);
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
            let url = id ?
                '{{ route('proyek-saluran.update', ['proyek_saluran' => ':id']) }}'.replace(':id', id) :
                '{{ route('proyek-saluran.store') }}';
            let method = id ? 'PUT' : 'POST';

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData(this);
            formData.append('_method', method);
            formData.append('_token', '{{ csrf_token() }}'); // ✅ tambahkan ini

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modalForm').modal('hide');
                    audio.play();
                    let msg = id ? "Data berhasil diupdate!" : "Data berhasil ditambahkan!";
                    toastr.success(msg, "BERHASIL", {
                        progressBar: true,
                        timeOut: 3500,
                        positionClass: "toast-bottom-right",
                    });
                    $('.data-table').DataTable().ajax.reload();
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

                        spinner.addClass('d-none');
                        btnText.text('Simpan');
                        submitBtn.prop('disabled', false);
                    }
                }
            });
        });


        // Hapus data
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<span class="swal-btn-text">Ya, Hapus</span>',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: false,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const confirmBtn = Swal.getConfirmButton();
                        const btnText = confirmBtn.querySelector('.swal-btn-text');

                        btnText.innerHTML =
                            `<span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span> Menghapus...`;
                        confirmBtn.disabled = true;

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function() {
                                audio.play();
                                toastr.success("Data telah dihapus!", "BERHASIL", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                $('.data-table').DataTable().ajax.reload(null,
                                    false);
                                Swal.close();
                            },
                            error: function() {
                                audio.play();
                                toastr.error("Gagal menghapus data.", "GAGAL!", {
                                    progressBar: true,
                                    timeOut: 3500,
                                    positionClass: "toast-bottom-right"
                                });

                                btnText.innerHTML = `Ya, Hapus`;
                                confirmBtn.disabled = false;
                            }
                        });
                    });
                }
            });
        });
    </script>
@endpush
