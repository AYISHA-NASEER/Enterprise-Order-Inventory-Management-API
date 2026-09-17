@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        <h2>Order Tracking</h2>

        <div class="card mt-4">
            <div class="card-body">

                <h4>Order #{{ $order->order_number }}</h4>

                <hr>

                <h5>Shipment Status</h5>

                <div class="mt-4">

                    {{-- Order --}}
                    <div class="mb-3">
                        <strong>✓ Order Confirmed</strong>
                    </div>

                    {{-- Payment --}}
                    <div class="mb-3">
                        <strong>✓ Payment Successful</strong>
                    </div>

                    {{-- Shipment --}}
                    <div class="mb-3">
                        @if($shipment && in_array($shipment->status, ['pending', 'shipped', 'in_transit', 'delivered']))
                            <strong>✓ Shipment Created</strong>
                        @else
                            <strong>○ Shipment Created</strong>
                        @endif
                    </div>

                    {{-- Shipped --}}
                    <div class="mb-3">
                        @if($shipment && in_array($shipment->status, ['shipped', 'in_transit', 'delivered']))
                            <strong>✓ Shipped</strong>
                        @else
                            <strong>○ Shipped</strong>
                        @endif
                    </div>

                    {{-- In Transit --}}
                    <div class="mb-3">
                        @if($shipment && in_array($shipment->status, ['in_transit', 'delivered']))
                            <strong>✓ In Transit</strong>
                        @else
                            <strong>○ In Transit</strong>
                        @endif
                    </div>

                    {{-- Delivered --}}
                    <div class="mb-3">
                        @if($shipment && $shipment->status === 'delivered')
                            <strong>✓ Delivered</strong>
                        @else
                            <strong>○ Delivered</strong>
                        @endif
                    </div>

                </div>

                <hr>

                @if($shipment)

                    <p>
                        <strong>Current Status:</strong>
                        {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                    </p>

                    <p>
                        <strong>Tracking Number:</strong>
                        {{ $shipment->tracking_number ?? 'Not available yet' }}
                    </p>

                    <p>
                        <strong>Carrier:</strong>
                        {{ $shipment->carrier ?? 'Not available yet' }}
                    </p>

                @else

                    <p>
                        Shipment has not been created yet.
                    </p>

                @endif

            </div>
        </div>

    </div>

@endsection