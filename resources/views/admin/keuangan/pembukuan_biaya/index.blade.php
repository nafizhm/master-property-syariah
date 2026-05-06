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
                                    <h3 class="font-weight-bold text-lg">Data Pembukuan Biaya</h3>

                                </div>
                            </div>
                            <div class="card-body">
                                <style>
                                    .table-horizontal {
                                        display: block;
                                        width: 100%;
                                        overflow-x: auto;
                                        -webkit-overflow-scrolling: touch;
                                        white-space: nowrap;
                                    }
                                </style>
                                <table class="table small table-horizontal table-bordered table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th width="50px">No</th>
                                            <th width="220px">Customer</th>

                                            <th width="140px">Harga Unit</th>
                                            <th width="140px">Booking Fee</th>
                                            <th width="140px">DP</th>
                                            <th width="140px">Diskon</th>
                                            <th width="140px">Bonus</th>
                                            <th width="160px">Biaya Custom</th>

                                            <th width="180px">Biaya KPR</th>
                                            <th width="180px">Biaya Notaris</th>
                                            <th width="180px">Pajak BPHTB</th>

                                            <th width="140px">PPN</th>
                                            <th width="160px">Biaya Lain</th>

                                            <th width="180px">Total Plafond</th>
                                            <th width="200px">Total Harga Unit</th>
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
            var table = $('.data-table').DataTable({
                processing: false,
                serverSide: false,
                responsive: false,
                ordering: false,
                ajax: "{{ route('pembukuan-biaya.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'harga_unit',
                        name: 'harga_unit',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'booking_fee',
                        name: 'booking_fee',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'dp',
                        name: 'dp',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'diskon',
                        name: 'diskon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bonus',
                        name: 'bonus',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'biaya_custom',
                        name: 'biaya_custom',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'biaya_kpr',
                        name: 'biaya_kpr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'biaya_notaris',
                        name: 'biaya_notaris',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pajak_bphtb',
                        name: 'pajak_bphtb',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ppn',
                        name: 'ppn',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'biaya_lain',
                        name: 'biaya_lain',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_plafond',
                        name: 'total_plafond',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_harga_unit',
                        name: 'total_harga_unit',
                        orderable: false,
                        searchable: false
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
