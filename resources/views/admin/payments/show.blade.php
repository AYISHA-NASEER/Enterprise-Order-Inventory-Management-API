@extends('layouts.app')

@section('content')

    <div class="container mt-4 mb-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="mb-1">Payment Details</h2>
                <p class="text-muted mb-0">
                    Complete information about this payment
                </p>
            </div>

            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-dark">
                ← Back to Payments
            </a>

        </div>


        {{-- Payment Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Payment Information</h5>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Payment ID --}}
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Payment ID
                        </label>

                        <div class="fw-bold">
                            #{{ $payment->id }}
                        </div>
                    </div>


                    {{-- Amount --}}
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Amount
                        </label>

                        <div class="fw-bold fs-5">
                            ₹{{ number_format($payment->amount, 2) }}
                        </div>
                    </div>


                    {{-- Status --}}
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Payment Status
                        </label>

                        <div>

                            @if($payment->status === 'paid')

                                <span class="badge bg-success">
                                    Paid
                                </span>

                            @elseif($payment->status === 'created')

                                <span class="badge bg-warning text-dark">
                                    Created
                                </span>

                            @elseif($payment->status === 'failed')

                                <span class="badge bg-danger">
                                    Failed
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ ucfirst($payment->status) }}
                                </span>

                            @endif

                        </div>
                    </div>


                    {{-- Created --}}
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Created At
                        </label>

                        <div>
                            {{ $payment->created_at?->format('d M Y, h:i A') }}
                        </div>
                    </div>


                    {{-- Updated --}}
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Updated At
                        </label>

                        <div>
                            {{ $payment->updated_at?->format('d M Y, h:i A') }}
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- Razorpay Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Razorpay Information</h5>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Razorpay Order ID --}}
                    <div class="col-md-6 mb-3">

                        <label class="text-muted">
                            Razorpay Order ID
                        </label>

                        <div>

                            @if($payment->razorpay_order_id)

                                <code>
                                        {{ $payment->razorpay_order_id }}
                                    </code>

                            @else

                                <span class="text-muted">
                                    Not available
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- Razorpay Payment ID --}}
                    <div class="col-md-6 mb-3">

                        <label class="text-muted">
                            Razorpay Payment ID
                        </label>

                        <div>

                            @if($payment->razorpay_payment_id)

                                <code>
                                        {{ $payment->razorpay_payment_id }}
                                    </code>

                            @else

                                <span class="text-muted">
                                    Not available
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Customer Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Customer Information</h5>
            </div>

            <div class="card-body">

                @if($payment->order?->user)

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Customer ID
                            </label>

                            <div class="fw-bold">
                                #{{ $payment->order->user->id }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Customer Name
                            </label>

                            <div class="fw-bold">
                                {{ $payment->order->user->name }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Email
                            </label>

                            <div>
                                {{ $payment->order->user->email }}
                            </div>

                        </div>

                    </div>

                @else

                    <p class="text-muted mb-0">
                        Customer information is not available.
                    </p>

                @endif

            </div>

        </div>


        {{-- Order Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-info text-dark">
                <h5 class="mb-0">Order Information</h5>
            </div>

            <div class="card-body">

                @if($payment->order)

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Order ID
                            </label>

                            <div class="fw-bold">
                                #{{ $payment->order->id }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Order Number
                            </label>

                            <div class="fw-bold">
                                {{ $payment->order->order_number }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Order Status
                            </label>

                            <div>

                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($payment->order->status) }}
                                </span>

                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Order Total
                            </label>

                            <div class="fw-bold">
                                ₹{{ number_format($payment->order->total_amount, 2) }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="text-muted">
                                Order Created
                            </label>

                            <div>
                                {{ $payment->order->created_at?->format('d M Y, h:i A') }}
                            </div>

                        </div>

                    </div>

                @else

                    <p class="text-muted mb-0">
                        Order information is not available.
                    </p>

                @endif

            </div>

        </div>


        {{-- Order Items --}}
        @if($payment->order && $payment->order->items)

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead class="table-light">

                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse($payment->order->items as $item)

                                                    <tr>

                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>


                                                        <td>

                                                            @if($item->product)

                                                                {{ $item->product->name }}

                                                            @else

                                                                <span class="text-muted">
                                                                    Product unavailable
                                                                </span>

                                                            @endif

                                                        </td>


                                                        <td>

                                                            @if($item->product)

                                                                {{ $item->product->sku }}

                                                            @else

                                                                —

                                                            @endif

                                                        </td>


                                                        <td>
                                                            {{ $item->quantity }}
                                                        </td>


                                                        <td>
                                                            ₹{{ number_format($item->unit_price, 2) }}
                                                        </td>


                                                        <td class="fw-bold">

                                                            ₹{{ number_format(
                                        $item->quantity * $item->unit_price,
                                        2
                                    ) }}

                                                        </td>

                                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6" class="text-center py-4 text-muted">

                                            No order items found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        @endif


        {{-- Shipment Information --}}
        @if($payment->order)

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Shipment Information</h5>
                </div>

                <div class="card-body">

                    @if($payment->order->shipment)

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="text-muted">
                                        Shipment ID
                                    </label>

                                    <div>
                                        #{{ $payment->order->shipment->id }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="text-muted">
                                        Provider Shipment ID
                                    </label>

                                    <div>
                                        {{ $payment->order->shipment->provider_shipment_id ?? '—' }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="text-muted">
                                        Tracking Number
                                    </label>

                                    <div>
                                        {{ $payment->order->shipment->tracking_number ?? '—' }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="text-muted">
                                        Carrier
                                    </label>

                                    <div>
                                        {{ $payment->order->shipment->carrier ?? '—' }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="text-muted">
                                        Shipment Status
                                    </label>

                                    <div>

                                        <span class="badge bg-secondary">

                                            {{ ucfirst(
                            $payment->order->shipment->status ?? 'unknown'
                        ) }}

                                        </span>

                                    </div>

                                </div>

                            </div>

                    @else

                        <div class="text-muted">
                            Shipment has not been created yet.
                        </div>

                    @endif

                </div>

            </div>

        @endif


        {{-- Bottom Button --}}
        <div class="text-center mb-4">

            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-dark">

                ← Back to All Payments

            </a>

        </div>

    </div>

@endsection