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
                                    <h3 class="font-weight-bold text-lg">Data Komisi</h3>
                                    <div class="d-flex align-items-center">
                                        @if ($permissions['tambah'])
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#modalForm">
                                                <i class="fas fa-plus"></i> Tambah Komisi
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal</th>
                                            <th>Customer</th>
                                            <th>% Inhouse</th>
                                            <th>Nominal Inhouse</th>
                                            <th>% Agent</th>
                                            <th>Nominal Agent</th>
                                            <th width="15%">Action</th>
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
    </div>

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-focus="false" aria-labelledby="modalFormLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <h5 class="modal-title text-white font-weight-bold" id="modalFormLabel">Form Komisi Marketing</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formData" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="primary_id" name="primary_id">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Komisi</label>
                            <div class="col-sm-3">
                                <input type="date" name="tanggal_komisi" id="tanggal_komisi" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Rekening</label>
                            <div class="col-sm-6">
                                <select name="id_bank" id="id_bank" class="form-select select-rekening">
                                    <option value=""></option>
                                    @foreach ($rekenings as $rekening)
                                        <option value="{{ $rekening->id }}">{{ $rekening->nama }} - {{ $rekening->no_rek }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Customer</label>
                            <div class="col-sm-5">
                                <select name="id_customer" id="id_customer" class="form-select select-customer">
                                    <option value=""></option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}" data-hrg_jual="{{ $c->hrg_jual }}"
                                            data-diskon="{{ $c->diskon }}" data-pajak_pph="{{ $c->pajak_pph }}"
                                            data-pajak_bphtb="{{ $c->pajak_bphtb }}"
                                            data-stt_bphtb="{{ $c->stt_free_pajak_bphtb }}"
                                            data-biaya_notaris="{{ $c->biaya_notaris }}"
                                            data-stt_notaris="{{ $c->stt_free_biaya_notaris }}"
                                            data-biaya_kpr="{{ $c->biaya_kpr }}"
                                            data-stt_kpr="{{ $c->stt_free_biaya_kpr }}"
                                            data-bonus="{{ $c->bonus_konsumen }}" data-ppn="{{ $c->ppn }}"
                                            data-biaya_lain="{{ $c->biaya_lain_lain }}"
                                            data-komisi="{{ $c->total_harga_komisi }}">
                                            {{ $c->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Harga Jual</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-primary rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="hrg_jual" id="hrg_jual"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Pajak PPH</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="pajak_pph" id="pajak_pph"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Pajak BPHTB</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="pajak_bphtb" id="pajak_bphtb"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Biaya Notaris</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="biaya_notaris" id="biaya_notaris"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Biaya KPR</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="biaya_kpr" id="biaya_kpr"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Bonus Konsumen</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="bonus_konsumen" id="bonus_konsumen"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Diskon</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="diskon" id="diskon"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">PPN</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="ppn" id="ppn"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Biaya lain-lain</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-danger rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="biaya_lain_lain" id="biaya_lain_lain"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Harga Bersih Komisi</label>
                            <div class="col-sm-5">
                                <div class="input-group border border-success rounded shadow-sm">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="total_harga_komisi" id="total_harga_komisi"
                                        class="form-control border-0 format-numer" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Persen Inhouse</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="persen_inhouse" id="persen_inhouse"
                                        class="form-control persentase">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-3 col-form-label">Nominal Inhouse</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="nominal_inhouse" id="nominal_inhouse"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Persen Agent</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="persen_agent" id="persen_agent"
                                        class="form-control persentase">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-3 col-form-label">Nominal Agent</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="nominal_agent" id="nominal_agent"
                                        class="form-control format-number">
                                </div>
                            </div>
                        </div>

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
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select-customer').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Customer",
            });
            $('.select-rekening').select2({
                theme: "bootstrap4",
                placeholder: "Pilih Rekening",
            });
        });

        $(function() {
            var permissions = @json($permissions);
            var showActionColumn = (permissions['edit'] == 1 || permissions['hapus'] == 1);

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                responsive: true,
                ordering: false,
                ajax: "{{ route('komisi.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'customer.nama_lengkap',
                        name: 'customer.nama_lengkap',
                        orderable: false,
                        searchable: true,
                    },
                    {
                        data: 'persen_inhouse',
                        name: 'persen_inhouse',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nominal_inhouse',
                        name: 'nominal_inhouse',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'persen_agent',
                        name: 'persen_agent',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nominal_agent',
                        name: 'nominal_agent',
                        orderable: false,
                        searchable: false,
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

        $(document).on('change', '#id_customer', function() {
            let opt = $(this).find(':selected');

            let hrg_jual = opt.data('hrg_jual') || 0;
            let komisi = opt.data('komisi') || 0;
            let diskon = opt.data('diskon') || 0;
            let pajak_pph = opt.data('pajak_pph') || 0;
            let pajak_bphtb = opt.data('stt_bphtb') == 1 ? (opt.data('pajak_bphtb') || 0) : 0;
            let biaya_notaris = opt.data('stt_notaris') == 1 ? (opt.data('biaya_notaris') || 0) : 0;
            let biaya_kpr = opt.data('stt_kpr') == 1 ? (opt.data('biaya_kpr') || 0) : 0;
            let bonus = opt.data('bonus') || 0;
            let ppn = opt.data('ppn') || 0;
            let biaya_lain = opt.data('biaya_lain') || 0;

            $('#hrg_jual').val(formatNumber(hrg_jual));
            $('#diskon').val(formatNumber(diskon));
            $('#pajak_pph').val(formatNumber(pajak_pph));
            $('#pajak_bphtb').val(formatNumber(pajak_bphtb));
            $('#biaya_notaris').val(formatNumber(biaya_notaris));
            $('#biaya_kpr').val(formatNumber(biaya_kpr));
            $('#bonus_konsumen').val(formatNumber(bonus));
            $('#ppn').val(formatNumber(ppn));
            $('#biaya_lain_lain').val(formatNumber(biaya_lain));

            $('#total_harga_komisi').val(formatNumber(komisi));
            recalcInhouse();
            recalcAgent();
        });

        $(document).on('input', '.persentase', function() {
            let val = $(this).val();
            val = val.replace(',', '.');
            val = val.replace(/[^0-9.]/g, '');
            let parts = val.split('.');
            if (parts.length > 2) val = parts[0] + '.' + parts[1];
            if (parts[1] && parts[1].length > 1) val = parts[0] + '.' + parts[1].substring(0, 1);
            $(this).val(val);
            recalcInhouse();
            recalcAgent();
        });

        function recalcInhouse() {
            let total = unformatNumber($('#total_harga_komisi').val());
            let persen = parseFloat($('#persen_inhouse').val()) || 0;
            let nominal = unformatNumber($('#nominal_inhouse').val()) || 0;

            if (document.activeElement.id === 'persen_inhouse') {
                nominal = total * (persen / 100);
                $('#nominal_inhouse').val(formatNumber(Math.round(nominal)));
            } else if (document.activeElement.id === 'nominal_inhouse') {
                persen = total ? (nominal / total) * 100 : 0;
                $('#persen_inhouse').val(persen.toFixed(1));
            }
        }

        function recalcAgent() {
            let total = unformatNumber($('#total_harga_komisi').val());
            let persen = parseFloat($('#persen_agent').val()) || 0;
            let nominal = unformatNumber($('#nominal_agent').val()) || 0;

            if (document.activeElement.id === 'persen_agent') {
                nominal = total * (persen / 100);
                $('#nominal_agent').val(formatNumber(Math.round(nominal)));
            } else if (document.activeElement.id === 'nominal_agent') {
                persen = total ? (nominal / total) * 100 : 0;
                $('#persen_agent').val(persen.toFixed(1));
            }
        }

        $(document).on('input', '#persen_inhouse, #nominal_inhouse', recalcInhouse);
        $(document).on('input', '#persen_agent, #nominal_agent', recalcAgent);

        $(document).on('click', '.edit-button', function() {
            var url = $(this).data('url');

            $.get(url, function(response) {
                if (response.status === 'success') {
                    $('#primary_id').val(response.data.id);
                    $('#tanggal_komisi').val(response.data.tanggal_komisi);
                    $('#id_customer').val(response.data.id_customer).trigger('change');
                    $('#id_bank').val(response.data.id_bank).trigger('change');
                    $('#persen_inhouse').val(response.data.persen_inhouse);
                    $('#nominal_inhouse').val(formatNumber(response.data.nominal_inhouse));
                    $('#persen_agent').val(response.data.persen_agent);
                    $('#nominal_agent').val(formatNumber(response.data.nominal_agent));

                    $('#modalForm').modal('show');
                }
            });
        });

        $('#modalForm').on('hidden.bs.modal', function() {
            $('#formData')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.form-select').val('').trigger('change');

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
            let url = id ? '{{ route('komisi.update', ['komisi' => ':id']) }}'.replace(
                    ':id',
                    id) :
                '{{ route('komisi.store') }}';
            let method = id ? 'PUT' : 'POST';

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
                success: function(response) {
                    $('#modalForm').modal('hide');
                    audio.play();
                    let msg = id ? "Komisi Marketing berhasil diupdate!" :
                        "Komisi Marketing berhasil ditambahkan!";
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
                            let input = $('#' + key);
                            input.addClass('is-invalid');
                            input.parent().find('.invalid-feedback').remove();
                            input.parent().append(
                                '<span class="invalid-feedback" role="alert"><strong>' +
                                val[0] + '</strong></span>'
                            );
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
                text: 'Komisi Marketing ini akan dihapus secara permanen!',
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
                                toastr.success("Komisi Marketing telah dihapus!",
                                    "BERHASIL", {
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
