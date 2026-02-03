@extends('admin.layout_admin')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
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
                                    <h3 class="font-weight-bold text-lg">Data Customer</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" id="id_wawancara" name="id_wawancara" value="{{ $data->id }}">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                                            readonly value="{{ $data->customer->nama_lengkap }}">
                                    </div>
                                    <label class="col-sm-1 col-form-label">NIK</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="nik" id="nik" class="form-control" readonly
                                            value="{{ $data->customer->nik }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Alamat KTP</label>
                                    <div class="col-sm-5">
                                        <textarea id="alamat_ktp" name="alamat_ktp" class="form-control" readonly rows="3">{{ $data->customer->alamat_ktp }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Lokasi Rumah</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="lokasi_rumah" id="lokasi_rumah" class="form-control"
                                            readonly
                                            value="{{ $data->customer->kavling->kode_kavling }} - {{ $data->customer->lokasi->nama_kavling }}">
                                    </div>
                                    <label class="col-sm-2 col-form-label">Harga Rumah</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="harga_rumah" id="harga_rumah" class="form-control"
                                            readonly
                                            value="{{ number_format($data->customer->kavling->hrg_jual, 0, ',', '.') }}">
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Tipe Bangunan</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="tipe_bangunan" id="tipe_bangunan" class="form-control"
                                            readonly value="{{ $data->customer->kavling->tipe_bangunan }}">
                                    </div>
                                    <label class="col-sm-1 col-form-label">Luas Tanah</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="luas_tanah" id="luas_tanah" class="form-control"
                                            readonly value="{{ $data->customer->kavling->luas_tanah }}">
                                    </div>
                                    <label class="col-sm-2 col-form-label">Luas Bangunan</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="luas_bangunan" id="luas_bangunan" class="form-control"
                                            readonly value="{{ $data->customer->kavling->luas_bangunan }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header p-3">
                                <div class="d-flex align-content-center justify-content-between">
                                    <h3 class="font-weight-bold text-lg">Data SP3K</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered w-100 small table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th width="3%">No</th>
                                            <th>Bank KPR</th>
                                            <th>ACC Plafon</th>
                                            <th>Tgl SP3K</th>
                                            <th>Tgl Expired</th>
                                            <th>Sisa Hari</th>
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
@endsection
@push('scripts')
    <script>
        $(function() {

            let id = $('#id_wawancara').val();
            let url = '{{ route('acc-bank.show', ['acc_bank' => ':id']) }}'.replace(':id', id)

            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                ordering: false,
                responsive: true,
                ajax: url,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'bankKPR',
                        name: 'bankKPR',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'acc_plafon',
                        name: 'acc_plafon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_terbit_sp3k',
                        name: 'tgl_terbit_sp3k',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl_expired',
                        name: 'tgl_expired',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sisa_hari',
                        name: 'sisa_hari',
                        orderable: false,
                        searchable: false,
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
    </script>
@endpush
