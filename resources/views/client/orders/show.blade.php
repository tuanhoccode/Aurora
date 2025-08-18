@extends('client.layouts.default')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fafafa;
            margin: 0;
            padding: 20px 0;
            color: #333;
        }

        #order-detail-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
        }

        .order-box {
            margin: 30px 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
        }

        .progress-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
            position: relative;
            padding: 0 20px;
        }

        .progress-bar::before {
            content: "";
            position: absolute;
            top: 50%;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .step {
            position: relative;
            z-index: 1;
            background: #fff;
            text-align: center;
            width: 120px;
        }

        .step .circle {
            width: 28px;
            height: 28px;
            background: #ee4d2d;
            border-radius: 50%;
            margin: 0 auto 10px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            position: relative;
        }

        .step.completed .circle {
            background: #ee4d2d;
        }

        .step.active .circle {
            background: #fff;
            border: 2px solid #ee4d2d;
            color: #ee4d2d;
        }

        .step p {
            font-size: 12px;
            margin: 5px 0 0;
            color: #666;
        }

        .order-info {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .order-info p {
            margin: 5px 0;
        }

        .timeline {
            border-left: 3px solid #ee4d2d;
            padding-left: 15px;
            margin: 20px 0 30px;
        }

        .timeline-item {
            margin-bottom: 20px;
            position: relative;
            padding-left: 15px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -20px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ee4d2d;
        }

        .timeline-item span {
            display: block;
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }

        .product {
            display: flex;
            gap: 15px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }

        .product img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .product-info {
            flex: 1;
            font-size: 14px;
            line-height: 1.4;
        }

        .price {
            text-align: right;
            color: #ee4d2d;
            font-weight: bold;
            min-width: 100px;
        }

        #order-detail-container .total {
            text-align: right;
            font-size: 18px;
            color: #ee4d2d;
            font-weight: bold;
            margin: 20px 0;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        #order-detail-container .order-header {
            display: flex;
            justify-content: space-between;
            padding: 16px;
            font-size: 14px;
            border-bottom: 1px solid #f5f5f5;
            background: #fff;
            border-radius: 2px 2px 0 0;
        }

        #order-detail-container .order-header h2 {
            margin: 0;
            font-size: 14px;
            color: #333;
            font-weight: normal;
        }

        #order-detail-container .order-status {
            color: #ee4d2d;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        #order-detail-container .status-pending {
            background: #ffc107;
            color: #000;
        }

        #order-detail-container .status-confirmed {
            background: #17a2b8;
            color: #fff;
        }

        #order-detail-container .status-shipping {
            background: #007bff;
            color: #fff;
        }

        #order-detail-container .status-delivered {
            background: #28a745;
            color: #fff;
        }

        #order-detail-container .status-cancelled {
            background: #dc3545;
            color: #fff;
        }

        /* Timeline ngang */
        .timeline-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
            padding: 20px 0;
            position: relative;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9e9e9e;
            font-size: 18px;
            margin-bottom: 8px;
        }

        /* Stronger rules to ensure green appears for completed/active */
        .timeline-step.active .timeline-icon {
            background: #16a34a !important;
            color: white !important;
            border: 2px solid #16a34a !important;
        }

        .timeline-step.completed .timeline-icon {
            background: #16a34a !important;
            color: white !important;
        }

        .timeline-content {
            text-align: center;
        }

        .timeline-title {
            font-size: 14px;
            color: #333;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .timeline-time {
            font-size: 12px;
            color: #757575;
        }

        .timeline-connector {
            flex: 1;
            position: relative;
            height: 2px;
            background: #e0e0e0;
            margin: 0 10px;
        }

        .cancelled-notice {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #fff5f5;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffebee;
        }

        .cancelled-notice i {
            font-size: 24px;
            color: #f44336;
            margin-right: 15px;
        }

        .cancelled-text {
            color: #d32f2f;
            font-weight: 500;
        }

        .cancelled-time {
            font-size: 13px;
            color: #9e9e9e;
            margin-top: 4px;
        }

        .connector-line {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            background: #16a34a;
            transition: width 0.3s ease;
        }

        .timeline-step.completed+.timeline-connector .connector-line {
            width: 100%;
        }

        /* Chi tiết trạng thái */
        .timeline-details {
            margin-top: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
        }

        .timeline-details h3 {
            padding: 15px;
            margin: 0;
            font-size: 16px;
            background: #f5f5f5;
            border-bottom: 1px solid #e0e0e0;
        }

        .status-detail-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .status-detail-item:last-child {
            border-bottom: none;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            margin-right: 15px;
            margin-top: 5px;
        }

        .status-content {
            flex: 1;
        }

        .status-title {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .status-time {
            font-size: 12px;
            color: #757575;
            margin-bottom: 4px;
        }

        .status-desc {
            font-size: 13px;
            color: #616161;
            line-height: 1.4;
        }

        /* Vertical track (left column) styles */
        .ship-board{display:grid;grid-template-columns:1.1fr 1.9fr;gap:28px;align-items:flex-start}
        .ship-addr{padding-right:24px;border-right:1px solid #e5e7eb}
        .ship-addr h3{margin:0 0 12px 0;font-size:20px}
        .ship-addr p{margin:6px 0;font-size:14px;color:#334155}
        .track-wrap{position:relative}
        .track-header{font-size:12px;color:#64748b;text-align:right;margin-bottom:6px}
        .track-list{margin:0;padding:0;list-style:none;position:relative}
        .track-list::before{content:"";position:absolute;left:14px;top:4px;bottom:4px;width:2px;background:#e5e7eb}
        .track-item{position:relative;padding-left:36px;margin:0 0 16px 0}
        .track-node{position:absolute;left:6px;top:0;width:16px;height:16px;border-radius:50%;background:#cbd5e1;border:2px solid #fff;box-shadow:0 0 0 1px #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:10px}
        .track-item.current .track-node{background:#16a34a;box-shadow:0 0 0 1px #16a34a;color:#fff}
        .track-item.done .track-node{background:#16a34a;box-shadow:0 0 0 1px #16a34a;color:#fff}
        .track-time{font-size:13px;color:#475569;margin-bottom:2px}
        .track-title{font-weight:600;font-size:14px;color:#0f766e;margin-bottom:2px}
        .track-desc{font-size:13px;color:#475569;line-height:1.45}
        .track-link{font-size:13px;color:#0ea5e9;text-decoration:none}
        .track-more{font-size:14px;margin-top:6px}
    </style>

<div class="container mt-4" style="max-width: 1200px;">
    <div id="order-detail-container">
    <div class="order-header" style="display: flex; align-items: center; gap: 15px;">
        <a href="{{ route('client.orders') }}" class="btn btn-light btn-sm" style="margin-right: 10px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>MÃ ĐƠN HÀNG: {{ $order->code }}</div>
        @php
            // Get the latest status from status history
            $latestStatus = $order->statusHistory->sortByDesc('created_at')->first();
            $currentStatusName = $latestStatus ? $latestStatus->status->name : 'Chờ xác nhận';
            $statusText = '';
            
            // Map status to display text
            if ($currentStatusName === 'Đã giao hàng' || in_array($currentStatusName, ['Nhận hàng thành công', 'Giao hàng thành công'])) {
                $statusText = 'ĐƠN HÀNG ĐÃ HOÀN THÀNH';
                
            } elseif ($currentStatusName === 'Đã hủy' || $order->cancellation_status !== null) {
                $statusText = 'ĐƠN HÀNG ĐÃ HỦY';
            } elseif (in_array($currentStatusName, ['Đang vận chuyển', 'Đang giao hàng', 'Đang giao'])) {
                $statusText = 'ĐANG GIAO HÀNG';
            } elseif (in_array($currentStatusName, ['Đã xác nhận', 'Chờ lấy hàng', 'Gửi hàng', 'Đã xác nhận thanh toán'])) {
                $statusText = 'ĐANG XỬ LÝ';
            } else {
                $statusText = 'CHỜ XÁC NHẬN';
            }
        @endphp
        <div class="order-status {{ strtolower(str_replace(' ', '-', $statusText)) }}">{{ $statusText }}</div>
    </div>


            <!-- Timeline -->
            <div class="timeline-horizontal">
                @php
                    // Lấy tất cả các trạng thái đơn hàng từ lịch sử và sắp xếp theo thời gian
                    $statuses = $order->statusHistory->sortBy('created_at');

                    // Lấy thời gian của từng trạng thái
                    $statusTimes = [];
                    foreach ($statuses as $status) {
                        $statusTimes[$status->status->name] = $status->created_at->setTimezone('Asia/Ho_Chi_Minh');
                    }

                    // Xác định trạng thái hiện tại của đơn hàng
                    $currentStatus = $order->currentStatus ? $order->currentStatus->status->name : 'Chờ xác nhận';
                    $currentStatusId = $order->currentStatus ? $order->currentStatus->status->id : 1;

                    // Danh sách các bước trong timeline
                    $timelineSteps = [
                        [
                            'id' => 1,
                            'icon' => 'fa-shopping-cart',
                            'title' => 'Chờ xác nhận',
                            'status' => 'Chờ xác nhận',
                            'active' => $currentStatusId == 1,
                            'completed' => $currentStatusId >= 1,
                            'time' => $order->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
                            'desc' => 'Đơn hàng mới được tạo, đang chờ xác nhận.',
                        ],
                        [
                            'id' => 2,
                            'icon' => 'fa-box',
                            'title' => 'Chờ lấy hàng',
                            'status' => 'Chờ lấy hàng',
                            'active' => $currentStatusId == 2,
                            'completed' => $currentStatusId >= 2 && $currentStatusId != 8, // Không hiển thị đã hoàn thành nếu đã hủy
                            'time' => isset($statusTimes['Chờ lấy hàng'])
                                ? $statusTimes['Chờ lấy hàng']->format('d/m/Y H:i')
                                : '',
                            'desc' => 'Đơn hàng đang chờ lấy hàng.',
                        ],
                        [
                            'id' => 3,
                            'icon' => 'fa-truck',
                            'title' => 'Đang giao',
                            'status' => 'Đang giao',
                            'active' => $currentStatusId == 3,
                            'completed' =>
                                ($currentStatusId >= 3 && $currentStatusId < 5) ||
                                ($currentStatusId > 5 && $currentStatusId != 8),
                            'time' => isset($statusTimes['Đang giao'])
                                ? $statusTimes['Đang giao']->format('d/m/Y H:i')
                                : '',
                            'desc' => 'Đơn hàng đang được giao.',
                        ],
                        [
                            'id' => 4,
                            'icon' => 'fa-check-circle',
                            'title' => 'Giao hàng thành công',
                            'status' => 'Giao hàng thành công',
                            'active' => $currentStatusId == 4,
                            'completed' => $currentStatusId == 4 || $currentStatusId == 7, // Chỉ hoàn thành khi đã giao hoặc đã hoàn tiền
                            'time' => isset($statusTimes['Giao hàng thành công'])
                                ? $statusTimes['Giao hàng thành công']->format('d/m/Y H:i')
                                : '',
                            'desc' => 'Đơn hàng đã giao thành công.',
                        ],
                    ];

                    // Lọc các bước không cần hiển thị
                    $filteredSteps = array_filter($timelineSteps, function ($step) use ($currentStatusId) {
                        // Nếu là trạng thái đã hủy, chỉ hiển thị trạng thái hủy
                        if ($currentStatusId == 8) {
                            return $step['id'] == 8;
                        }

                        // Ẩn các bước trả hàng nếu không phải là đơn hàng trả
                        if (in_array($step['id'], [5, 6, 7]) && $currentStatusId < 5) {
                            return false;
                        }

                        // Ẩn bước đã gửi hàng nếu không phải là đơn hàng đã gửi
                        if ($step['id'] == 9 && $currentStatusId != 9) {
                            return false;
                        }

                        return true;
                    });

                    // Sắp xếp lại mảng theo ID
                    usort($filteredSteps, function ($a, $b) {
                        return $a['id'] <=> $b['id'];
                    });

                    $timelineSteps = array_values($filteredSteps);

                    // Nếu đơn hàng đã hủy, chỉ hiển thị thông báo hủy
                    $cancelledTime = isset($statusTimes['Đã hủy']) ? $statusTimes['Đã hủy']->format('H:i d/m/Y') : '';
                @endphp

                @if ($currentStatus === 'Đã hủy')
                    <div class="container-fluid py-3 mb-4"
                        style="border-left: 4px solid #fecaca; background-color: #fef2f2;">
                        <div class="container">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-ban mt-1 me-3" style="color: #f87171; font-size: 1.25rem;"></i>
                                <div>
                                    <h5 class="mb-1" style="color: #b91c1c; font-weight: 500;">Đơn hàng đã bị hủy</h5>
                                    @if (!empty($cancelledTime))
                                        <div class="text-sm mb-2" style="color: #6b7280;">Thời gian hủy:
                                            {{ $cancelledTime }}</div>
                                    @endif
                                    @if ($order->cancel_reason)
                                        <div
                                            style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #fecaca; margin-top: 8px;">
                                            <p class="mb-1"><span style="font-weight: 500; color: #b91c1c;">Lý do
                                                    hủy:</span> {{ $order->cancel_reason }}</p>
                                            @if ($order->cancel_note)
                                                <p class="text-sm mt-1" style="color: #6b7280;"><i
                                                        class="fas fa-info-circle mr-1"
                                                        style="color: #fca5a5;"></i>{{ $order->cancel_note }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @foreach ($timelineSteps as $index => $step)
                        @php
                          // Provide inline styles to ensure completed/active appear green
                          $isCompleted = !empty($step['completed']);
                          $isActive = !empty($step['active']);
                          $iconStyle = '';
                          if ($isCompleted) {
                            $iconStyle = 'background:#16a34a;color:#fff';
                          } elseif ($isActive) {
                            $iconStyle = 'background:#fff;color:#16a34a;border:2px solid #16a34a';
                          }
                        @endphp
                        <div
                            class="timeline-step {{ $step['completed'] ? 'completed' : '' }} {{ $step['active'] ? 'active' : '' }}">
                            <div class="timeline-icon" style="{{ $iconStyle }}">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">{{ $step['title'] }}</div>
                                @if ($step['time'])
                                    <div class="timeline-time">{{ $step['time'] }}
                                        @if ($step['status'] === 'Chờ xác nhận' || $step['status'] === $currentStatus)
                                            <div>Hệ thống</div>
                                        @endif
                                    </div>
                                @endif
                                @if (isset($step['desc']))
                                    <div class="timeline-desc">{{ $step['desc'] }}</div>
                                @endif
                            </div>
                        </div>
                        @if (!$loop->last)
                            <div class="timeline-connector">
                                <div class="connector-line"></div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>


            <!-- Thông tin người nhận -->

            <div class="order-info" style="border:none;padding:0;margin-bottom:20px">
              <div class="ship-board">
                <!-- Cột trái: Địa chỉ nhận hàng -->
                <div class="ship-addr">
                    <div class="order-info">
                        <h3>Địa Chỉ Nhận Hàng</h3>
                        <p style="font-weight:600;font-size:15px">{{ $order->fullname }}</p>
                        <p>(+84) {{ preg_replace('/^(0|\+84)/','', $order->phone_number) }}</p>
                        <p>{{ $order->address }}{{ $order->city ? ', '.$order->city : '' }}</p>
                    </div>
                   <!-- Trạng thái thanh toán -->
                <div class="order-info">
                    <h3>Trạng Thái Thanh Toán</h3>
                    @if ($order->is_paid)
                        <p><i class="fas fa-check-circle" style="color: #28a745;"></i> <strong>Đã thanh toán</strong></p>
                        @if ($order->payment)
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment->name ?? 'Không xác định' }}</p>
                        @endif
                        <p><strong>Ngày thanh toán:</strong> {{ $order->updated_at->format('H:i d/m/Y') }}</p>
                    @else
                        <p><i class="fas fa-times-circle" style="color: #dc3545;"></i> <strong>Chưa thanh toán</strong></p>
                        @if ($order->payment)
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment->name ?? 'Không xác định' }}</p>
                        @endif
                    @endif

                    @if ($order->is_refunded)
                        <div
                            style="margin-top: 10px; padding: 10px; background-color: #fff8e1; border-left: 4px solid #ffc107;">
                            <p><i class="fas fa-info-circle" style="color: #ffc107;"></i> <strong>Đơn hàng đã được hoàn
                                    tiền</strong></p>
                            @if ($order->is_refunded_canceled)
                                <p>Lý do hủy: {{ $order->cancel_reason ?? 'Không có thông tin' }}</p>
                                @if ($order->cancel_note)
                                    <p>Ghi chú: {{ $order->cancel_note }}</p>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>

                <!-- Cột phải: Timeline trạng thái dọc -->
                <div class="track-wrap">
                  @php
                    $carrier = data_get($order, 'shipping_carrier');
                    $tracking = data_get($order, 'tracking_code');
                  @endphp
                  @if(!empty($carrier) || !empty($tracking))
                    <div class="track-header">
                      {{ $carrier ?? '' }}<br>
                      {{ $tracking ?? '' }}
                    </div>
                  @endif

                  @php
                    // Số mục hiển thị mặc định (bạn muốn thay đổi mặc định thì sửa biến này)
                    $__initialVisible = 3;

                    $__events = [];
                    $__events[] = [
                      'time'  => $order->created_at->setTimezone('Asia/Ho_Chi_Minh'),
                      'title' => 'Đơn hàng đã được đặt',
                      'desc'  => 'Đơn #'.$order->code.' đã tạo thành công.',
                    ];
                    foreach ($order->statusHistory as $h) {
                      $__events[] = [
                        'time'  => $h->created_at->setTimezone('Asia/Ho_Chi_Minh'),
                        'title' => $h->status->name,
                        'desc'  => $h->note ?? 'Cập nhật trạng thái đơn hàng.',
                      ];
                    }
                    $__events = collect($__events)->sortByDesc('time')->values();
                    $__showAll = request()->has('show_all_events');
                  @endphp

                  <ul class="track-list">
                    @foreach ($__events as $i => $e)
                      @php
                        // Mark items as done/current based on index and current status predictable logic
                        $isCurrent = $i === 0;
                        $isDone = $i === 0 ? ($currentStatusName && in_array($currentStatusName, ['Đã giao hàng','Giao hàng thành công','Nhận hàng thành công'])) : false;
                      @endphp
                      <li class="track-item {{ $isCurrent ? 'current' : '' }} {{ $isDone ? 'done' : '' }}" @if(!$__showAll && $i >= $__initialVisible) style="display:none" @endif>
                        <div class="track-node">@if($isDone || $isCurrent)<i class="fas fa-check"></i>@endif</div>
                        <div class="track-time">{{ $e['time']->format('H:i d-m-Y') }}</div>

                        @if($i===0 && in_array($currentStatusName ?? '', ['Đã giao hàng','Giao hàng thành công','Nhận hàng thành công']))
                          <div class="track-title" style="color:#16a34a">Đã giao</div>
                          <div class="track-desc">Giao hàng thành công</div>
                          <div class="track-desc">Người nhận hàng: {{ $order->fullname }}</div>
                        @else
                          <div class="track-title" style="color:{{ $i===0 ? '#16a34a' : '#0f766e' }}">{{ $e['title'] }}</div>
                          @if(!empty($e['desc']))<div class="track-desc">{{ $e['desc'] }}</div>@endif
                        @endif
                      </li>
                    @endforeach
                  </ul>

                  @if($__events->count() > $__initialVisible)
                    <div class="track-more">
                      <a class="track-link" href="{{ $__showAll ? url()->current() : url()->current().'?show_all_events=1' }}">
                        {{ $__showAll ? 'Ẩn bớt' : 'Xem thêm' }}
                      </a>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="order-box"
                style="margin: 20px 0; background: #fff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);">
                <div class="shop-header"
                    style="padding: 12px; border-bottom: 1px solid #f5f5f5; display: flex; align-items: center;">
                    <div
                        style="background: #ee4d2d; color: #fff; font-weight: bold; font-size: 12px; padding: 2px 6px; border-radius: 2px; margin-right: 8px;">
                        Sản phẩm đã đặt</div>
                </div>

                @foreach ($order->items as $item)
                    <div style="padding: 12px; border-bottom: 1px solid #f5f5f5; display: flex;">
                        <img src="{{ $item->product->image_url ?? asset('assets2/img/product/2/default.png') }}"
                            alt="{{ $item->product->name }}"
                            style="width: 80px; height: 80px; object-fit: cover; margin-right: 12px; border: 1px solid #f0f0f0;">
                        <div style="flex: 1;">
                            <div style="font-size: 14px; color: #333;">{{ $item->product->name }}</div>
                            @php
                                $variantAttributes = $item->attributes_variant
                                    ? json_decode($item->attributes_variant, true)
                                    : [];
                            @endphp
                            @if (!empty($variantAttributes))
                                <div style="font-size: 12px; color: #888; margin: 4px 0;">
                                    Phân loại:
                                    @foreach ($variantAttributes as $attrName => $attrValue)
                                        {{ $attrName }}: {{ $attrValue }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </div>
                            @endif
                            <div style="font-size: 12px; color: #666;">Số lượng: {{ $item->quantity }}</div>
                        </div>
                        <div style="text-align: right; white-space: nowrap; margin-left: 12px;">
                            @if ($item->price_variant && $item->price_variant != $item->price)
                                <div style="color: #999; text-decoration: line-through; margin-bottom: 4px;">
                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                </div>
                            @endif
                            <div style="color: #ee4d2d; font-weight: bold;">
                                {{ number_format(($item->price_variant ?? $item->price) * $item->quantity, 0, ',', '.') }}đ
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Tổng tiền -->
                <div style="padding: 12px; text-align: right; font-size: 14px; border-top: 1px solid #f5f5f5;">
                    @php
                        $subtotal = $order->items->sum(function ($item) {
                            return ($item->price_variant ?? $item->price) * $item->quantity;
                        });
                        $total = $subtotal + $order->shipping_fee - $order->discount_amount;
                    @endphp
                    <div style="margin: 6px 0;">
                        <span style="color: #666;">Tổng tiền hàng</span>
                        <span style="color: #333; margin-left: 12px;">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    @if ($order->shipping_fee > 0)
                        <div style="margin: 6px 0;">
                            <span style="color: #666;">Phí vận chuyển</span>
                            <span
                                style="color: #333; margin-left: 12px;">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                    @if ($order->discount_amount > 0)
                        <div style="margin: 6px 0;">
                            <span style="color: #666;">Giảm giá</span>
                            <span
                                style="color: #26aa99; margin-left: 12px;">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                    <div
                        style="font-size: 18px; color: #ee4d2d; font-weight: bold; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #f0f0f0;">
                        Thành tiền: {{ number_format($total, 0, ',', '.') }}đ
                    </div>
                    
                </div>
                
                @if ($order->payment_method)
                    <div
                        style="padding: 12px; border-top: 1px solid #f5f5f5; font-size: 14px; text-align: right; color: #666;">
                        Phương thức thanh toán: <strong>{{ $order->payment_method }}</strong>
                    </div>
                @endif
                @if ($order->is_paid == 0 && $order->payment_id == 2)
                    <a href="{{ route('checkout.retry-payment', $order->code) }}"
                        class="btn btn-primary checkout__btn-main">
                        <i class="fas fa-redo"></i> Quay lại thanh toán
                    </a>
                @endif
            </div>

            <!-- Form hủy đơn hàng -->
            @if (
                $order->cancellation_status === null &&
                    (optional(optional($order->currentStatus)->status)->code === 'PENDING' ||
                        optional(optional($order->currentStatus)->status)->code === 'PROCESSING'))
                <div
                    style="margin-top: 30px; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px; background-color: #f8d7da;">
                    <h4 style="margin-top: 0; color: #721c24;">Hủy đơn hàng</h4>
                    <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 15px;">
                            <label for="reason" style="display: block; margin-bottom: 5px; font-weight: 600;">Lý do hủy
                                đơn</label>
                            <select name="reason" id="reason" required
                                style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;">
                                <option value="">Chọn lý do...</option>
                                <option value="Không thích sản phẩm">Không thích sản phẩm</option>
                                <option value="Giao hàng chậm">Giao hàng chậm</option>
                                <option value="Đổi ý">Đổi ý</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="note" style="display: block; margin-bottom: 5px; font-weight: 600;">Ghi chú
                                (tùy chọn)</label>
                            <textarea name="note" id="note" rows="3"
                                style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;"></textarea>
                        </div>
                        <button type="submit"
                            style="background-color: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: 600;">
                            Gửi yêu cầu hủy đơn
                        </button>
                    </form>
                </div>
            @elseif($order->cancellation_status === 'rejected')
                <div
                    style="margin-top: 30px; padding: 15px; background-color: #f8d7da; border-radius: 4px; border-left: 4px solid #dc3545;">
                    <p style="margin: 0; color: #721c24;">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Yêu cầu hủy đơn đã bị từ chối</strong>
                        @if ($order->cancellation_processed_at)
                            <br><small class="text-muted">Cập nhật lần cuối:
                                {{ $order->cancellation_processed_at->format('H:i d/m/Y') }}</small>
                        @endif
                    </p>
                </div>
            @endif

            @if(in_array(optional(optional($order->currentStatus)->status)->code, ['COMPLETED', 'CANCELLED', 'DELIVERED']))
                <div class="mt-4">
                    <form action="{{ route('client.orders.reorder', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-redo-alt me-1"></i> Mua lại đơn hàng
                        </button>
                    </form>
                    <p class="text-muted mt-2">
                        <small>Bấm để thêm tất cả sản phẩm còn bán vào giỏ hàng</small>
                    </p>
                </div>
            @endif
        </div>

        @if(session('reorder_status'))
            <div class="alert alert-{{ session('reorder_status')['type'] }} mt-3">
                {{ session('reorder_status')['message'] }}
                @if(session('reorder_status')['type'] === 'success')
                    <a href="{{ route('client.cart') }}" class="alert-link">Xem giỏ hàng</a>
                @endif
            </div>
        @endif

@endsection
