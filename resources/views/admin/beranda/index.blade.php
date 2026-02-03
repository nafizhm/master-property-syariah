@extends('admin.layout_admin')
@section('content')
    <style>
        .card-custom {
            width: 190px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            user-select: none;
            gap: 8px;
            font-size: 16px;
        }

        .card-custom:hover {
            border-color: #6610f2;
            box-shadow: 0 3px 10px rgb(102 16 242 / 0.3);
        }

        .icon-orange {
            color: #f09000;
        }

        .icon-green {
            color: #28a745;
        }

        .icon-red {
            color: #dc3545;
        }

        .icon-blue {
            color: #007bff;
        }

        .icon-gray {
            color: #6c757d;
        }

        .icon-info {
            color: #7abaff;
        }

        .icon-yellow {
            color: #ffbf00;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid text-center my-4">
                <h5>Halo, <strong>{{ $username }}</strong></h5>
                <h4><strong>Aktivitas apa yang ingin Anda lakukan?</strong></h4>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid d-flex justify-content-center">
                <div class="row g-3" style="max-width: 900px;">

                    @php
                        $userId = auth()->id();

                        $hakAkses = \App\Models\HakAkses::with('menu')
                            ->where('id_user', $userId)
                            ->where('lihat', 1)
                            ->where('beranda', 1)
                            ->get();

                        $colors = [
                            'icon-red',
                            'icon-blue',
                            'icon-green',
                            'icon-yellow',
                            'icon-orange',
                            'icon-purple',
                            'icon-gray',
                            'icon-info',
                        ];
                    @endphp

                    @foreach ($hakAkses as $akses)
                        @if ($akses->menu)
                            @php
                                $randomColor = $colors[array_rand($colors)];
                            @endphp

                            <div class="col-12 col-sm-6 col-md-3 mb-3 d-flex justify-content-center">
                                <div class="card-custom px-3 py-2 text-center"
                                    onclick="window.location='{{ route($akses->menu->route_name) }}'" role="button"
                                    tabindex="0"
                                    onkeypress="if(event.key === 'Enter'){ window.location='{{ route($akses->menu->route_name) }}'}">

                                    <i class="fas {{ $akses->menu->icon }} {{ $randomColor }}"
                                        style="font-size: 16px;"></i>
                                    <strong style="font-size: 16px;">{{ $akses->menu->title }}</strong>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
