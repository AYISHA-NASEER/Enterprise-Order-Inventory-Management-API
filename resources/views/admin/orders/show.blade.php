@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2>Order Details</h2>

                <p class="text-muted mb-0">
                    View complete information about this order.
                </p>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark">
                ← Back to Orders
            </a>

        </div>


        {{-- Order Summary --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Order Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Order ID --}}
                    <div class="col-md-4">

                        <strong>Order ID</strong>

                        <div>
                            #{{ $order->id }}
                        </div>

                    </div>


                    {{-- Order Number --}}
                    <div class="col-md-4">

                        <strong>Order Number</strong>

                        <div>
                            {{ $order->order_number }}
                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-4">

                        <strong>Status</strong>

                        <div class="mt-1">

                            @if($order->status === 'paid')

                                <span class="badge bg-success">
                                    Paid
                                </span>

                            @elseif($order->status === 'pending')

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            @elseif($order->status === 'payment_failed')

                                <span class="badge bg-danger">
                                    Payment Failed
                                </span>

                            @elseif($order->status === 'cancelled')

                                <span class="badge bg-secondary">
                                    Cancelled
                                </span>

                            @else

                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($order->status) }}
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- Total --}}
                    <div class="col-md-4">

                        <strong>Total Amount</strong>

                        <div class="text-success fs-5">
                            ₹{{ number_format((float) $order->total_amount, 2) }}
                        </div>

                    </div>


                    {{-- Created --}}
                    <div class="col-md-4">

                        <strong>Created At</strong>

                        <div>
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </div>

                    </div>


                    {{-- Updated --}}
                    <div class="col-md-4">

                        <strong>Last Updated</strong>

                        <div>
                            {{ $order->updated_at->format('d M Y, h:i A') }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Customer Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Customer Information
                </h5>
            </div>

            <div class="card-body">

                @if($order->user)

                    <div class="row g-3">

                        <div class="col-md-4">

                            <strong>Customer ID</strong>

                            <div>
                                {{ $order->user->id }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <strong>Name</strong>

                            <div>
                                {{ $order->user->name }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <strong>Email</strong>

                            <div>
                                {{ $order->user->email }}
                            </div>

                        </div>

                    </div>

                @else

                    <p class="text-muted mb-0">
                        Customer information not available.
                    </p>

                @endif

            </div>

        </div>


        {{-- Order Items --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Order Items
                </h5>
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

                            @forelse($order->items as $index => $item)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
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

                                            -

                                        @endif

                                    </td>

                                    <td>
                                        {{ $item->quantity }}
                                    </td>

                                    <td>
                                        ₹{{ number_format((float) $item->unit_price, 2) }}
                                    </td>

                                    <td>
                                        ₹{{ number_format((float) $item->total_price, 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center text-muted py-4">
                                        No items found for this order.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Payment Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Payment Information
                </h5>
            </div>

            <div class="card-body">

                @if($order->payment)

                    <div class="row g-3">

                        <div class="col-md-4">

                            <strong>Payment ID</strong>

                            <div>
                                #{{ $order->payment->id }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <strong>Payment Status</strong>

                            <div>

                                @if($order->payment->status === 'paid')

                                    <span class="badge bg-success">
                                        Paid
                                    </span>

                                @elseif($order->payment->status === 'initiated')

                                    <span class="badge bg-warning text-dark">
                                        Initiated
                                    </span>

                                @elseif($order->payment->status === 'failed')

                                    <span class="badge bg-danger">
                                        Failed
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="col-md-4">

                            <strong>Amount</strong>

                            <div>
                                ₹{{ number_format((float) $order->payment->amount, 2) }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <strong>Razorpay Order ID</strong>

                            <div class="text-break">
                                {{ $order->payment->razorpay_order_id ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <strong>Razorpay Payment ID</strong>

                            <div class="text-break">
                                {{ $order->payment->razorpay_payment_id ?? '-' }}
                            </div>

                        </div>

                    </div>

                @else

                    <p class="text-muted mb-0">
                        No payment has been created for this order.
                    </p>

                @endif

            </div>

        </div>


        {{-- Shipment Information --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Shipment Information
                </h5>
            </div>

            <div class="card-body">

                @if($order->shipment)

                    <div class="row g-3">

                        <div class="col-md-4">

                            <strong>Shipment ID</strong>

                            <div>
                                #{{ $order->shipment->id }}
                            </div>

                        </div>


                        @if(isset($order->shipment->status))

                            <div class="col-md-4">

                                <strong>Shipment Status</strong>

                                <div>
                                    {{ ucfirst($order->shipment->status) }}
                                </div>

                            </div>

                        @endif


                        @if(isset($order->shipment->tracking_number))

                            <div class="col-md-4">

                                <strong>Tracking Number</strong>

                                <div>
                                    {{ $order->shipment->tracking_number }}
                                </div>

                            </div>

                        @endif

                    </div>

                @else

                    <p class="text-muted mb-0">
                        No shipment has been created for this order.
                    </p>

                @endif

            </div>

        </div>


        {{-- Back Button --}}
        <div class="text-center mb-5">

            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                ← Back to Orders
            </a>

        </div>

    </div>

@endsection