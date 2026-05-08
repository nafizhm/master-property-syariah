@extends('layouts.app')

@section('title', 'Siteplan Penjualan - Taman Jivva Kemlaten')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
            background: url('{{ asset('config_media/booking-bg.png') }}') no-repeat center center fixed !important;
            background-size: cover !important;
            min-height: 100vh;
        }

        .siteplan-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 15px 50px;
        }

        .siteplan-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.97);
        }

        .siteplan-header {
            background: linear-gradient(135deg, #0d5c2e 0%, #1a7a42 50%, #228B4a 100%);
            padding: 24px 30px;
            text-align: center;
        }

        .siteplan-header h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }

        .siteplan-header p {
            color: rgba(255, 255, 255, 0.8);
            margin: 8px 0 0;
            font-size: 0.85rem;
        }

        .siteplan-body {
            padding: 20px 30px 30px;
        }

        /* Tabs Styling */
        .nav-tabs {
            border-bottom: 2px solid #e8f5e9;
            gap: 5px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            background-color: #f9fafb;
            color: #1a7a42;
        }

        .nav-tabs .nav-link.active {
            background-color: transparent;
            color: #1a7a42;
            border-bottom: 3px solid #1a7a42;
            font-weight: 700;
        }

        /* SVG Container */
        .svg-card-wrapper {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .svg-view-container {
            width: 100%;
            height: 90vh;
            min-height: 750px;
            overflow: hidden;
            position: relative;
            background: #f8fafc;
        }

        .svg-view-container svg {
            width: 100%;
            height: 100%;
            display: block;
            cursor: grab;
            transition: transform 0.1s ease-out;
            touch-action: none;
            user-select: none;
            -webkit-user-drag: none;
            transform-origin: 0 0;
        }

        /* Floating Components */
        .legend-box {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 15px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 210px;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
        }

        .legend-box.hidden {
            transform: translateX(250px);
        }

        .legend-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background: #1a7a42;
            color: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 999;
            border: none;
        }

        .legend-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: #111827;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .legend-label {
            font-size: 0.75rem;
            color: #4b5563;
            font-weight: 500;
        }

        /* Popup Box */
        #popupOverlay {
            position: fixed;
            z-index: 9999;
            display: none;
            pointer-events: none;
        }

        #popupBox {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            width: 300px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            pointer-events: all;
            border: 1px solid #e5e7eb;
        }

        /* Popup arrow (triangle) */
        .popup-arrow {
            position: absolute;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }

        /* Arrow at bottom (popup above target) */
        .popup-arrow.arrow-bottom {
            bottom: -10px;
            top: auto;
            border-top: 10px solid #fff;
            border-bottom: none;
        }

        /* Arrow at top (popup below target) */
        .popup-arrow.arrow-top {
            top: -10px;
            bottom: auto;
            border-bottom: 10px solid #fff;
            border-top: none;
        }

        /* Active kavling highlight */
        .detail-button.kavling-active polygon,
        .detail-button.kavling-active path {
            filter: brightness(1.3) drop-shadow(0 0 6px rgba(0, 0, 0, 0.5));
            stroke: #ffffff !important;
            stroke-width: 2px !important;
            transition: filter 0.2s ease, stroke 0.2s ease;
        }

        .detail-button polygon,
        .detail-button path {
            transition: filter 0.2s ease, stroke 0.2s ease;
        }

        /* Hover effect for blocks */
        .detail-button:hover polygon,
        .detail-button:hover path {
            filter: brightness(1.15);
            cursor: pointer;
        }

        #popupClose {
            position: absolute;
            top: 10px;
            right: 12px;
            background: #f3f4f6;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            transition: background 0.15s, color 0.15s;
        }

        #popupClose:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .popup-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1a5c30;
            margin-bottom: 15px;
        }

        .popup-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.8rem;
        }

        .popup-label {
            color: #6b7280;
        }

        .popup-value {
            font-weight: 600;
            color: #111827;
            text-align: right;
        }

        .popup-price {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-price .value {
            font-size: 1rem;
            font-weight: 800;
            color: #16a34a;
        }

        .popup-pricing {
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px dashed #e5e7eb;
        }

        .popup-pricing-title {
            font-weight: 700;
            font-size: 0.8rem;
            color: #1a5c30;
            margin-bottom: 10px;
        }

        .popup-price-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 6px 8px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .popup-price-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
        }

        .popup-price-value {
            font-size: 0.85rem;
            font-weight: 700;
            color: #16a34a;
        }

        .btn-reset {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .btn-reset:hover {
            background: #fff;
            border-color: #1a7a42;
            color: #1a7a42;
        }

        .text-white-svg text {
            fill: #ffffff !important;
        }

        @media (max-width: 768px) {
            .siteplan-container {
                padding: 15px 10px;
            }

            .siteplan-header {
                padding: 20px;
            }

            .siteplan-body {
                padding: 15px;
            }

            .svg-view-container {
                height: 50vh;
                min-height: 400px;
            }

            .legend-box {
                bottom: 80px;
                right: 20px;
            }

            .legend-toggle {
                bottom: 20px;
                right: 20px;
            }
        }

        /* Mobile Tooltip Styles */
        @media (max-width: 768px) {
            #popupBox {
                width: 260px;
                padding: 15px;
                opacity: 0.85;
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }

            .popup-title {
                font-size: 0.85rem;
                margin-bottom: 12px;
            }

            .popup-row {
                margin-bottom: 6px;
                font-size: 0.7rem;
            }

            .popup-label {
                font-size: 0.7rem;
            }

            .popup-value {
                font-size: 0.7rem;
            }

            .sold-badge {
                padding: 10px;
                font-size: 0.75rem;
            }

            .popup-pricing {
                margin-top: 12px;
                padding-top: 10px;
            }

            .popup-pricing-title {
                font-size: 0.7rem;
                margin-bottom: 8px;
            }

            .popup-price-item {
                padding: 5px 6px;
                margin-bottom: 6px;
            }

            .popup-price-label {
                font-size: 0.65rem;
            }

            .popup-price-value {
                font-size: 0.7rem;
            }

            #popupClose {
                width: 22px;
                height: 22px;
                font-size: 14px;
                top: 8px;
                right: 10px;
            }
        }

        /* Extra small devices */
        @media (max-width: 480px) {
            font-size: 0.65rem;
        }
        }

        /* Sold Badge Style - Simplified */
        .status-badge-sold {
            margin-top: 15px;
            display: none;
        }

        .sold-badge {
            background: #dc3545;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .sold-badge i {
            margin-right: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="siteplan-container">
        <div class="siteplan-card">
            {{-- Header (Matching Booking) --}}
            <div class="siteplan-header">
                <h4>Siteplan Penjualan</h4>
                <p>Silakan pilih lokasi perumahan dan klik pada kavling untuk melihat detail informasi.</p>
            </div>

            <div class="siteplan-body">
                {{-- Tabs --}}
                <ul class="nav nav-tabs" id="siteplan-tabs" role="tablist">
                    @foreach ($lokasiKavling as $index => $kav)
                        <li class="nav-item">
                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}" id="tab-{{ $kav->id }}"
                                data-toggle="pill" href="#pane-{{ $kav->id }}" role="tab">
                                {{ $kav->nama_kavling }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="siteplan-tabContent">
                    @foreach ($lokasiKavling as $index => $kav)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="pane-{{ $kav->id }}"
                            role="tabpanel">

                            <div class="svg-card-wrapper">
                                <div class="svg-view-container svg-container" id="svg-container-{{ $kav->id }}">
                                    <button class="btn btn-reset reset-button">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset Siteplan
                                    </button>
                                    <button class="btn btn-reset download-button" style="left: 150px;">
                                        <i class="fas fa-download mr-1"></i> Download Siteplan
                                    </button>

                                    {{-- SVG Render --}}
                                    @if ($kav->masterSvg)
                                        {!! str_replace(['[[lebar]]', '[[tinggi]]'], ['100%', '100%'], $kav->masterSvg->header_svg) !!}

                                        @foreach ($kav->kavlingPeta as $pt)
                                            @php
                                                $warna = $pt->siteplan_color;
                                            @endphp

                                            <a href="javascript:void(0);"
                                                class="detail-button {{ $pt->siteplan_text_color === '#ffffff' ? 'text-white-svg' : '' }}"
                                                data-url="{{ route('public.siteplan.show', $pt->id) }}">
                                                {!! str_replace(
                                                    ['[[1]]', '[[2]]', '[[3]]', '[[4]]'],
                                                    [$pt->map, $warna, $pt->matrik, $pt->kode_kavling],
                                                    $pt->jenis_map == 'polygon' ? $kav->masterSvg->polygon_svg : $kav->masterSvg->path_svg,
                                                ) !!}
                                            </a>
                                        @endforeach

                                        {!! $kav->masterSvg->footer_svg !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <button class="legend-toggle" onclick="toggleLegend()">
        <i class="fas fa-info-circle"></i>
    </button>
    <div class="legend-box hidden" id="legendBox">
        <div class="legend-title">Keterangan Status</div>
        @foreach ($legend as $item)
            <div class="legend-item">
                <div class="legend-color" style="background-color: {{ $item->warna }}"></div>
                <div class="legend-label">{{ $item->status_progres }}</div>
            </div>
        @endforeach
        <button class="btn btn-xs btn-block mt-3 text-muted" onclick="toggleLegend()">Tutup</button>
    </div>

    {{-- Detail Popup --}}
    <div id="popupOverlay">
        <div id="popupBox">
            <button id="popupClose" onclick="closePopup()">&times;</button>
            <div class="popup-arrow"></div>

            <div id="popup_content_standard">
                <div class="popup-title">Detail Kavling</div>

                <div class="popup-row">
                    <span class="popup-label">Perumahan</span>
                    <span class="popup-value" id="p_nama_kavling">-</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Kode Kavling</span>
                    <span class="popup-value" id="p_kode_kavling">-</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Tipe</span>
                    <span class="popup-value"><span id="p_tipe_bangunan">-</span></span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Luas Tanah</span>
                    <span class="popup-value"><span id="p_luas_tanah">-</span> m²</span>
                </div>
                <div class="popup-row" id="row_dimensi" style="display: none;">
                    <span class="popup-label">Dimensi</span>
                    <span class="popup-value"><span id="p_dimensi">-</span></span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Luas Bangunan</span>
                    <span class="popup-value"><span id="p_luas_bangunan">-</span> m²</span>
                </div>

                <div id="mark_sold_container" class="status-badge-sold">
                    <div class="sold-badge">
                        <i class="fas fa-times-circle"></i> UNIT TERJUAL (SOLD)
                    </div>
                </div>

                <div class="popup-pricing" id="pricing_wrapper">
                    <div class="popup-pricing-title">Harga</div>
                    <div class="popup-price-item" id="row_cash_price" style="display: none;">
                        <span class="popup-price-label">Cash</span>
                        <span class="popup-price-value"><span id="p_cash_currency">Rp</span> <span
                                id="p_cash_price">0</span></span>
                    </div>
                    <div class="popup-price-item" id="row_kpr_price" style="display: none;">
                        <span class="popup-price-label">KPR</span>
                        <span class="popup-price-value"><span id="p_kpr_currency">Rp</span> <span
                                id="p_kpr_price">0</span></span>
                    </div>
                    <div class="popup-price-item" id="row_inhouse_price" style="display: none;">
                        <span class="popup-price-label">Inhouse</span>
                        <span class="popup-price-value"><span id="p_inhouse_currency">Rp</span> <span
                                id="p_inhouse_price">0</span></span>
                    </div>
                    <div class="popup-price-item" id="row_no_price">
                        <span class="popup-price-label">Harga</span>
                        <span class="popup-price-value text-muted">Belum Tersedia</span>
                    </div>
                </div>
            </div>

            <div id="popup_content_block_b" style="display: none;">
                <div class="popup-title">Detail Kavling</div>

                <div class="popup-row">
                    <span class="popup-label">Perumahan</span>
                    <span class="popup-value">Taman Jivva Kemlaten</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Kode Kavling</span>
                    <span class="popup-value" id="pb_kode_kavling">-</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Tipe</span>
                    <span class="popup-value">Peony 2BR / 3BR (2lantai)</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Luas Tanah</span>
                    <span class="popup-value"><span id="pb_luas_tanah">-</span> m²</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Dimensi</span>
                    <span class="popup-value" id="pb_dimensi">-</span>
                </div>
                <div class="popup-row">
                    <span class="popup-label">Luas Bangunan</span>
                    <span class="popup-value">51 m² / 60 m²</span>
                </div>

                <div id="pb_mark_sold_container" class="status-badge-sold">
                    <div class="sold-badge">
                        <i class="fas fa-times-circle"></i> UNIT TERJUAL (SOLD)
                    </div>
                </div>

                <div class="popup-pricing" id="pb_pricing_wrapper">
                    <div class="popup-pricing-title">Harga</div>
                    <div class="popup-price-item">
                        <span class="popup-price-label">Cash</span>
                        <span class="popup-price-value">870.000.000 / 910.000.000</span>
                    </div>
                    <div class="popup-price-item">
                        <span class="popup-price-label">KPR</span>
                        <span class="popup-price-value">895.000.000 / 935.000.000</span>
                    </div>
                    <div class="popup-price-item">
                        <span class="popup-price-label">Inhouse</span>
                        <span class="popup-price-value">911.000.000 / 953.000.000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentTargetElement = null;
        let popupUpdateInterval = null;

        function toggleLegend() {
            $('#legendBox').toggleClass('hidden');
        }

        function updatePopupPosition() {
            if (!currentTargetElement || !$('#popupOverlay').is(':visible')) return;

            const targetRect = currentTargetElement.getBoundingClientRect();
            const popupBox = document.getElementById('popupBox');
            const popupWidth = popupBox.offsetWidth;
            const popupHeight = popupBox.offsetHeight;
            const arrow = $('.popup-arrow');

            const targetCenterX = targetRect.left + (targetRect.width / 2);
            const targetCenterY = targetRect.top + (targetRect.height / 2);

            let left = targetCenterX - (popupWidth / 2);
            let top = targetCenterY - popupHeight - 15;

            const margin = 15;
            const winW = $(window).width();
            const winH = $(window).height();

            // Clamp horizontal position
            if (left < margin) left = margin;
            else if (left + popupWidth > winW - margin) left = winW - popupWidth - margin;

            // Calculate arrow offset so it points to the target center
            let arrowLeft = targetCenterX - left;
            arrowLeft = Math.max(20, Math.min(arrowLeft, popupWidth - 20));

            if (top < margin) {
                // Show popup below the target, arrow on top
                top = targetCenterY + 15;
                arrow.removeClass('arrow-bottom').addClass('arrow-top');
            } else {
                // Show popup above the target, arrow on bottom
                arrow.removeClass('arrow-top').addClass('arrow-bottom');
            }

            arrow.css({
                left: arrowLeft + 'px',
                transform: 'translateX(-50%)'
            });
            $('#popupOverlay').css({
                left: left + 'px',
                top: top + 'px'
            });
        }

        $(document).on('click', '.detail-button', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Remove previous highlight
            $('.detail-button.kavling-active').removeClass('kavling-active');
            // Add highlight to clicked block
            $(this).addClass('kavling-active');

            let url = $(this).data('url');
            currentTargetElement = this;

            $.get(url, function(res) {
                if (res.success) {
                    const isSold = parseInt(res.data.status) === 2; // STATUS_SOLD = 2
                    const hasAnyPrice = res.cash_price > 0 || res.kpr_price > 0 || res.inhouse_price > 0;
                    const isBlockB = res.data.kode_kavling.startsWith('B') && !res.data.kode_kavling
                        .startsWith('Bc');

                    if (isBlockB) {
                        $('#popup_content_standard').hide();
                        $('#popup_content_block_b').show();

                        $('#pb_kode_kavling').text(res.data.kode_kavling);
                        $('#pb_luas_tanah').text(res.data.luas_tanah);
                        $('#pb_dimensi').text(res.dimension || '-');

                        if (isSold) {
                            $('#pb_pricing_wrapper').hide();
                            $('#pb_mark_sold_container').show();
                        } else {
                            $('#pb_mark_sold_container').hide();
                            $('#pb_pricing_wrapper').show();
                        }
                    } else {
                        $('#popup_content_block_b').hide();
                        $('#popup_content_standard').show();

                        $('#p_nama_kavling').text(res.data.lokasi.nama_kavling);
                        $('#p_kode_kavling').text(res.data.kode_kavling);
                        $('#p_tipe_bangunan').text(res.data.tipe_bangunan);
                        $('#p_luas_tanah').text(res.data.luas_tanah);
                        $('#p_luas_bangunan').text(res.data.luas_bangunan);

                        // Dimension
                        if (res.dimension) {
                            $('#p_dimensi').text(res.dimension);
                            $('#row_dimensi').show();
                        } else {
                            $('#row_dimensi').hide();
                        }

                        // Pricing Logic
                        if (isSold) {
                            $('#pricing_wrapper').hide();
                            $('#mark_sold_container').show();
                        } else {
                            $('#mark_sold_container').hide();
                            $('#pricing_wrapper').show();

                            // Cash
                            if (res.cash_price > 0) {
                                $('#p_cash_price').text(parseFloat(res.cash_price).toLocaleString('id-ID'));
                                $('#row_cash_price').show();
                            } else {
                                $('#row_cash_price').hide();
                            }

                            // KPR
                            if (res.kpr_price > 0) {
                                $('#p_kpr_price').text(parseFloat(res.kpr_price).toLocaleString('id-ID'));
                                $('#row_kpr_price').show();
                            } else {
                                $('#row_kpr_price').hide();
                            }

                            // Inhouse
                            if (res.inhouse_price > 0) {
                                $('#p_inhouse_price').text(parseFloat(res.inhouse_price).toLocaleString(
                                    'id-ID'));
                                $('#row_inhouse_price').show();
                            } else {
                                $('#row_inhouse_price').hide();
                            }

                            // Show/hide "no price" message
                            if (hasAnyPrice) {
                                $('#row_no_price').hide();
                            } else {
                                $('#row_no_price').show();
                            }
                        }
                    }

                    $('#popupOverlay').fadeIn(200, function() {
                        updatePopupPosition();
                    });

                    if (popupUpdateInterval) clearInterval(popupUpdateInterval);
                    popupUpdateInterval = setInterval(updatePopupPosition, 50);
                }
            });
        });

        function closePopup() {
            $('#popupOverlay').fadeOut(200);
            // Remove kavling highlight
            $('.detail-button.kavling-active').removeClass('kavling-active');
            currentTargetElement = null;
            if (popupUpdateInterval) clearInterval(popupUpdateInterval);
        }

        $(document).on('click', function(e) {
            if ($(e.target).closest('#popupBox').length === 0 && !$(e.target).hasClass('detail-button')) {
                closePopup();
            }
        });

        $(window).on('resize scroll', updatePopupPosition);
        $('a[data-toggle="pill"]').on('shown.bs.tab', closePopup);

        // Auto-update position on SVG zoom/pan
        if (window.MutationObserver) {
            const observer = new MutationObserver(() => {
                if ($('#popupOverlay').is(':visible')) updatePopupPosition();
            });
            $(document).ready(() => {
                $('svg').each(function() {
                    observer.observe(this, {
                        attributes: true,
                        attributeFilter: ['transform', 'style']
                    });
                });
            });
        }

        /**
         * Download SVG as PNG
         */
        $(document).on('click', '.download-button', function() {
            const $container = $(this).closest('.svg-container');
            const svg = $container.find('svg')[0];
            const name = $('#siteplan-tabs .nav-link.active').text().trim() || 'siteplan';

            if (!svg) return;

            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...').prop('disabled', true);

            // Clone SVG to avoid modifying the original
            const clonedSvg = svg.cloneNode(true);
            clonedSvg.removeAttribute('style'); // Remove transforms

            // Get dimensions from viewBox
            const viewBox = svg.viewBox.baseVal;
            const vbW = viewBox.width || 2000;
            const vbH = viewBox.height || 2000;

            // Target max dimension (to avoid large file size and browser limits)
            const maxDim = 2500;
            let canvasW, canvasH;
            if (vbW > vbH) {
                canvasW = maxDim;
                canvasH = vbH * (maxDim / vbW);
            } else {
                canvasH = maxDim;
                canvasW = vbW * (maxDim / vbH);
            }

            // Function to convert image to base64 inside SVG
            const processImages = async (svgClone) => {
                const images = svgClone.querySelectorAll('image');
                for (let img of images) {
                    const href = img.getAttribute('xlink:href') || img.getAttribute('href');
                    if (href && !href.startsWith('data:')) {
                        try {
                            const response = await fetch(href);
                            if (!response.ok) throw new Error('Image not found');
                            const blob = await response.blob();
                            const base64 = await new Promise(resolve => {
                                const reader = new FileReader();
                                reader.onloadend = () => resolve(reader.result);
                                reader.readAsDataURL(blob);
                            });
                            img.setAttribute('xlink:href', base64);
                            img.setAttribute('href', base64);
                        } catch (e) {
                            console.warn('Failed to convert image to base64', e);
                            // If image is missing, we might want to remove it or use a placeholder
                        }
                    }
                }
            };

            processImages(clonedSvg).then(() => {
                const svgData = new XMLSerializer().serializeToString(clonedSvg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = canvasW;
                canvas.height = canvasH;

                const img = new Image();
                const svgBlob = new Blob([svgData], {
                    type: 'image/svg+xml;charset=utf-8'
                });
                const url = URL.createObjectURL(svgBlob);

                img.onload = function() {
                    ctx.fillStyle = '#f8fafc'; // Match container background
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    const pngUrl = canvas.toDataURL('image/png');
                    if (pngUrl.length < 100) {
                        alert('Gagal membuat gambar. Canvas mungkin terlalu besar.');
                        btn.html(originalHtml).prop('disabled', false);
                        return;
                    }

                    const downloadLink = document.createElement('a');
                    downloadLink.href = pngUrl;
                    downloadLink.download = `Siteplan-${name}.png`;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);

                    URL.revokeObjectURL(url);
                    btn.html(originalHtml).prop('disabled', false);
                };

                img.onerror = function(err) {
                    console.error('Image load error', err);
                    alert('Gagal mendownload gambar. Silakan coba lagi.');
                    btn.html(originalHtml).prop('disabled', false);
                };

                img.src = url;
            });
        });
    </script>
    <script src="{{ asset('assets/svg_1.js') }}"></script>
@endpush
