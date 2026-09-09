@extends('layouts.app')

@section('content')

    <div class="container-fluid mt-4">

        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">
                    📋 Paid Orders
                </h2>

                <p class="text-muted mb-0">
                    View all customer orders that have been successfully paid
                    and are ready for fulfillment.
                </p>

            </div>

            <a href="{{ route('warehouse.dashboard') }}" class="btn btn-secondary">

                ← Dashboard

            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- SUCCESS MESSAGE --}}
        {{-- ===================================================== --}}

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- ERROR MESSAGE --}}
        {{-- ===================================================== --}}

        @if(session('error'))

            <div class="alert alert-danger">
                {{ session('error') }}
            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- PAID ORDERS --}}
        {{-- ===================================================== --}}

        @forelse($orders as $order)

            <div class="card shadow-sm mb-4">

                {{-- ================================================= --}}
                {{-- ORDER HEADER --}}
                {{-- ================================================= --}}

                <div class="card-header bg-light">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="mb-1 fw-bold">

                                📦 Order
                                #{{ $order->order_number }}

                            </h5>

                            <small class="text-muted">

                                Order ID:
                                {{ $order->id }}

                                @if($order->created_at)

                                    |
                                    {{ $order->created_at->format('d M Y, h:i A') }}

                                @endif

                            </small>

                        </div>


                        {{-- ORDER STATUS --}}

                        <span class="badge bg-success fs-6">

                            ✓ {{ ucfirst($order->status) }}

                        </span>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- ORDER BODY --}}
                {{-- ================================================= --}}

                <div class="card-body">


                    {{-- ================================================= --}}
                    {{-- CUSTOMER + TOTAL --}}
                    {{-- ================================================= --}}

                    <div class="row g-4 mb-4">

                        {{-- CUSTOMER --}}
                        <div class="col-md-6">

                            <div class="card h-100 border">

                                <div class="card-body">

                                    <h6 class="fw-bold mb-3">
                                        👤 Customer
                                    </h6>

                                    @if($order->user)

                                        <p class="mb-1">

                                            <strong>
                                                Name:
                                            </strong>

                                            {{ $order->user->name }}

                                        </p>

                                        <p class="mb-0">

                                            <strong>
                                                Email:
                                            </strong>

                                            {{ $order->user->email }}

                                        </p>

                                    @else

                                        <p class="text-muted mb-0">
                                            Customer information not available.
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- ORDER TOTAL --}}
                        <div class="col-md-6">

                            <div class="card h-100 border">

                                <div class="card-body">

                                    <h6 class="fw-bold mb-3">
                                        💰 Order Total
                                    </h6>

                                    <h4 class="text-success fw-bold">

                                        ₹{{ number_format(
                (float) $order->total_amount,
                2
            ) }}

                                    </h4>

                                    <small class="text-muted">
                                        Total amount paid by customer.
                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- PRODUCTS --}}
                    {{-- ================================================= --}}

                    <div class="card border mb-4">

                        <div class="card-header">

                            <h6 class="mb-0 fw-bold">
                                📦 Products
                            </h6>

                        </div>


                        <div class="card-body p-0">

                            @if($order->items->isEmpty())

                                <div class="p-3 text-muted">
                                    No products found for this order.
                                </div>

                            @else

                                <div class="table-responsive">

                                    <table class="table table-bordered mb-0">

                                        <thead class="table-light">

                                            <tr>

                                                <th>
                                                    Product
                                                </th>

                                                <th>
                                                    SKU
                                                </th>

                                                <th>
                                                    Quantity
                                                </th>

                                                <th>
                                                    Unit Price
                                                </th>

                                                <th>
                                                    Total
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            @foreach($order->items as $item)

                                                                    <tr>

                                                                        <td>

                                                                            <strong>

                                                                                {{ $item->product->name
                                                    ?? 'Unknown Product'
                                                                                            }}

                                                                            </strong>

                                                                        </td>


                                                                        <td>

                                                                            {{ $item->product->sku
                                                    ?? '-'
                                                                                        }}

                                                                        </td>


                                                                        <td>

                                                                            {{ $item->quantity }}

                                                                        </td>


                                                                        <td>

                                                                            ₹{{ number_format(
                                                    (float) $item->unit_price,
                                                    2
                                                ) }}

                                                                        </td>


                                                                        <td>

                                                                            <strong>

                                                                                ₹{{ number_format(
                                                    (float) $item->total_price,
                                                    2
                                                ) }}

                                                                            </strong>

                                                                        </td>

                                                                    </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- PAYMENT --}}
                    {{-- ================================================= --}}

                    <div class="card border mb-4">

                        <div class="card-header bg-success text-white">

                            <h6 class="mb-0 fw-bold">
                                💳 Payment
                            </h6>

                        </div>


                        <div class="card-body">

                            @if($order->payment)

                                            <div class="row g-3">

                                                {{-- PAYMENT STATUS --}}
                                                <div class="col-md-4">

                                                    <strong>
                                                        Payment Status
                                                    </strong>

                                                    <div class="mt-1">

                                                        <span class="badge bg-success">

                                                            ✓
                                                            {{ ucfirst(
                                    $order->payment->status
                                ) }}

                                                        </span>

                                                    </div>

                                                </div>


                                                {{-- PAYMENT AMOUNT --}}
                                                <div class="col-md-4">

                                                    <strong>
                                                        Amount
                                                    </strong>

                                                    <div class="mt-1">

                                                        ₹{{ number_format(
                                    (float) $order->payment->amount,
                                    2
                                ) }}

                                                    </div>

                                                </div>


                                                {{-- RAZORPAY PAYMENT ID --}}
                                                <div class="col-md-4">

                                                    <strong>
                                                        Razorpay Payment ID
                                                    </strong>

                                                    <div class="mt-1">

                                                        @if($order->payment->razorpay_payment_id)

                                                            <code>
                                                                            {{ $order->payment->razorpay_payment_id }}
                                                                        </code>

                                                        @else

                                                            <span class="text-muted">
                                                                Not available
                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>


                                                {{-- RAZORPAY ORDER ID --}}
                                                <div class="col-md-12">

                                                    <strong>
                                                        Razorpay Order ID
                                                    </strong>

                                                    <div class="mt-1">

                                                        @if($order->payment->razorpay_order_id)

                                                            <code>
                                                                            {{ $order->payment->razorpay_order_id }}
                                                                        </code>

                                                        @else

                                                            <span class="text-muted">
                                                                Not available
                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                            @else

                                <div class="alert alert-warning mb-0">

                                    Payment record not found.

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- SHIPMENT --}}
                    {{-- ================================================= --}}

                    <div class="card border">

                        <div class="card-header bg-info">

                            <h6 class="mb-0 fw-bold">
                                🚚 Shipment
                            </h6>

                        </div>


                        <div class="card-body">

                            @if($order->shipment)

                                            <div class="row g-3">

                                                {{-- SHIPMENT STATUS --}}
                                                <div class="col-md-3">

                                                    <strong>
                                                        Status
                                                    </strong>

                                                    <div class="mt-1">

                                                        @php

                                                            $shipmentStatus =
                                                                $order->shipment->status;

                                                            $statusClass = match (
                                                            $shipmentStatus
                                                            ) {

                                                                'pending'
                                                                => 'bg-secondary',

                                                                'processing'
                                                                => 'bg-warning text-dark',

                                                                'shipped'
                                                                => 'bg-primary',

                                                                'delivered'
                                                                => 'bg-success',

                                                                'cancelled'
                                                                => 'bg-danger',

                                                                default
                                                                => 'bg-secondary',

                                                            };

                                                        @endphp


                                                        <span class="badge {{ $statusClass }}">

                                                            {{ ucfirst(
                                    $shipmentStatus
                                ) }}

                                                        </span>

                                                    </div>

                                                </div>


                                                {{-- TRACKING NUMBER --}}
                                                <div class="col-md-3">

                                                    <strong>
                                                        Tracking Number
                                                    </strong>

                                                    <div class="mt-1">

                                                        @if($order->shipment->tracking_number)

                                                            <code>

                                                                            {{ $order->shipment->tracking_number }}

                                                                        </code>

                                                        @else

                                                            <span class="text-muted">
                                                                Not available
                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>


                                                {{-- CARRIER --}}
                                                <div class="col-md-3">

                                                    <strong>
                                                        Carrier
                                                    </strong>

                                                    <div class="mt-1">

                                                        {{ $order->shipment->carrier
                                    ?? 'Not available'
                                                                }}

                                                    </div>

                                                </div>


                                                {{-- PROVIDER ID --}}
                                                <div class="col-md-3">

                                                    <strong>
                                                        Provider Shipment ID
                                                    </strong>

                                                    <div class="mt-1">

                                                        @if(
                                                                                $order->shipment
                                                                                    ->provider_shipment_id
                                                                            )

                                                                            <code>

                                                                                            {{
                                                            $order->shipment
                                                                ->provider_shipment_id
                                                                                            }}

                                                                                        </code>

                                                        @else

                                                            <span class="text-muted">
                                                                Not available
                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                            @else

                                <div class="alert alert-warning mb-0">

                                    🚚 Shipment has not been created yet.

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


        @empty

            {{-- ================================================= --}}
            {{-- NO ORDERS --}}
            {{-- ================================================= --}}

            <div class="alert alert-info">

                <h5 class="fw-bold">
                    📋 No Paid Orders
                </h5>

                <p class="mb-0">

                    There are currently no paid customer orders
                    waiting for fulfillment.

                </p>

            </div>

        @endforelse

    </div>

@endsection