@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">My Orders</h2>
            <p class="text-muted mb-0">
                View your orders, payments and shipment status.
            </p>
        </div>

        <a href="{{ route('customer.products') }}"
           class="btn btn-outline-primary">
            Continue Shopping
        </a>
    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    @if($orders->isEmpty())

        <div class="card shadow-sm">
            <div class="card-body text-center py-5">

                <h4>No orders yet</h4>

                <p class="text-muted">
                    You haven't placed any orders yet.
                </p>

                <a href="{{ route('customer.products') }}"
                   class="btn btn-primary">
                    Browse Products
                </a>

            </div>
        </div>

    @else

        @foreach($orders as $order)

            <div class="card shadow-sm mb-4">

                {{-- ============================= --}}
                {{-- ORDER HEADER --}}
                {{-- ============================= --}}

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-md-3">
                            <small class="text-muted">
                                Order Number
                            </small>

                            <div class="fw-bold">
                                {{ $order->order_number }}
                            </div>
                        </div>


                        <div class="col-md-2">

                            <small class="text-muted">
                                Status
                            </small>

                            <div class="mt-1">

                                @if($order->status === 'pending')

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @elseif($order->status === 'paid')

                                    <span class="badge bg-success">
                                        Paid
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

                                    <span class="badge bg-secondary">
                                        {{ ucfirst($order->status) }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="col-md-2">

                            <small class="text-muted">
                                Items
                            </small>

                            <div class="fw-bold">
                                {{ $order->items->sum('quantity') }}
                            </div>

                        </div>


                        <div class="col-md-2">

                            <small class="text-muted">
                                Total
                            </small>

                            <div class="fw-bold text-success">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </div>

                        </div>


                        <div class="col-md-3 text-md-end mt-3 mt-md-0">

                            {{-- PAYMENT BUTTON --}}

                            @if(
                                $order->status === 'pending' ||
                                $order->status === 'payment_failed'
                            )

                                <form
                                    action="{{ route('customer.orders.pay', $order->id) }}"
                                    method="POST"
                                    class="d-inline"
                                >

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-success">

                                        💳 Pay Now

                                    </button>

                                </form>

                            @elseif($order->status === 'paid')

                                <span class="text-success fw-bold">

                                    ✓ Payment Complete

                                </span>

                            @endif

                        </div>

                    </div>


                    <hr>


                    {{-- ============================= --}}
                    {{-- ORDER ITEMS --}}
                    {{-- ============================= --}}

                    <h6 class="fw-bold mb-3">
                        Order Items
                    </h6>

                    @foreach($order->items as $item)

                        <div class="d-flex justify-content-between
                                    align-items-center
                                    border-bottom
                                    py-2">

                            <div>

                                {{ $item->product->name }}

                                <span class="text-muted">
                                    × {{ $item->quantity }}
                                </span>

                            </div>

                            <div class="fw-bold">

                                ₹{{ number_format($item->total_price, 2) }}

                            </div>

                        </div>

                    @endforeach


                    {{-- ============================= --}}
                    {{-- ORDER TOTAL --}}
                    {{-- ============================= --}}

                    <div class="text-end mt-3">

                        <strong>
                            Order Total:
                        </strong>

                        <span class="text-success fw-bold ms-2">

                            ₹{{ number_format($order->total_amount, 2) }}

                        </span>

                    </div>


                    {{-- ============================= --}}
                    {{-- PAYMENT INFORMATION --}}
                    {{-- ============================= --}}

                    @if($order->payment)

                        <div class="alert
                            {{ $order->payment->status === 'paid'
                                ? 'alert-success'
                                : 'alert-light' }}
                            mt-4 mb-0">

                            <div class="fw-bold mb-2">
                                💳 Payment
                            </div>

                            <div>

                                Status:

                                @if($order->payment->status === 'paid')

                                    <span class="badge bg-success">
                                        Paid
                                    </span>

                                @elseif($order->payment->status === 'failed')

                                    <span class="badge bg-danger">
                                        Failed
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    @endif


                    {{-- ============================= --}}
                    {{-- SHIPMENT SECTION --}}
                    {{-- ============================= --}}

                    @if($order->shipment)

                        <div class="card border-primary mt-4">

                            <div class="card-header bg-primary text-white">

                                <strong>
                                    🚚 Shipment
                                </strong>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    {{-- Shipment Status --}}
                                    <div class="col-md-4 mb-3">

                                        <small class="text-muted d-block">
                                            Shipment Status
                                        </small>

                                        @if($order->shipment->status === 'pending')

                                            <span class="badge bg-warning text-dark mt-1">
                                                Processing
                                            </span>

                                        @elseif($order->shipment->status === 'processing')

                                            <span class="badge bg-warning text-dark mt-1">
                                                Processing
                                            </span>

                                        @elseif($order->shipment->status === 'shipped')

                                            <span class="badge bg-primary mt-1">
                                                Shipped
                                            </span>

                                        @elseif($order->shipment->status === 'delivered')

                                            <span class="badge bg-success mt-1">
                                                Delivered
                                            </span>

                                        @elseif($order->shipment->status === 'cancelled')

                                            <span class="badge bg-danger mt-1">
                                                Cancelled
                                            </span>

                                        @else

                                            <span class="badge bg-secondary mt-1">
                                                {{ ucfirst($order->shipment->status) }}
                                            </span>

                                        @endif

                                    </div>


                                    {{-- Carrier --}}
                                    <div class="col-md-4 mb-3">

                                        <small class="text-muted d-block">
                                            Carrier
                                        </small>

                                        <strong>

                                            {{ $order->shipment->carrier ?? 'Not assigned' }}

                                        </strong>

                                    </div>


                                    {{-- Tracking --}}
                                    <div class="col-md-4 mb-3">

                                        <small class="text-muted d-block">
                                            Tracking Number
                                        </small>

                                        <strong>

                                            {{ $order->shipment->tracking_number ?? 'Not available' }}

                                        </strong>

                                    </div>

                                </div>


                                {{-- Provider Shipment ID --}}
                                @if($order->shipment->provider_shipment_id)

                                    <div class="mt-2">

                                        <small class="text-muted">
                                            Provider Shipment ID
                                        </small>

                                        <div>
                                            {{ $order->shipment->provider_shipment_id }}
                                        </div>

                                    </div>

                                @endif


                                {{-- Shipment Timeline --}}

                                <div class="mt-4">

                                    <h6 class="fw-bold">
                                        Delivery Progress
                                    </h6>


                                    <div class="row text-center mt-3">

                                        {{-- Processing --}}
                                        <div class="col">

                                            @if(
                                                in_array(
                                                    $order->shipment->status,
                                                    ['pending', 'processing', 'shipped', 'delivered']
                                                )
                                            )

                                                <div class="fs-4">
                                                    🟢
                                                </div>

                                            @else

                                                <div class="fs-4">
                                                    ⚪
                                                </div>

                                            @endif

                                            <small>
                                                Processing
                                            </small>

                                        </div>


                                        {{-- Shipped --}}
                                        <div class="col">

                                            @if(
                                                in_array(
                                                    $order->shipment->status,
                                                    ['shipped', 'delivered']
                                                )
                                            )

                                                <div class="fs-4">
                                                    🟢
                                                </div>

                                            @else

                                                <div class="fs-4">
                                                    ⚪
                                                </div>

                                            @endif

                                            <small>
                                                Shipped
                                            </small>

                                        </div>


                                        {{-- Delivered --}}
                                        <div class="col">

                                            @if($order->shipment->status === 'delivered')

                                                <div class="fs-4">
                                                    🟢
                                                </div>

                                            @else

                                                <div class="fs-4">
                                                    ⚪
                                                </div>

                                            @endif

                                            <small>
                                                Delivered
                                            </small>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                    @elseif($order->status === 'paid')

                        {{-- Payment is complete but shipment job may still be running --}}

                        <div class="alert alert-info mt-4 mb-0">

                            🚚

                            <strong>
                                Shipment is being prepared.
                            </strong>

                            <br>

                            <small>
                                Your payment has been received.
                                Shipment details will appear here shortly.
                            </small>

                        </div>

                    @endif


                </div>

            </div>

        @endforeach

    @endif

</div>

@endsection