@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="fw-bold">
                    🔔 My Notifications
                </h2>

                <p class="text-muted mb-0">
                    View your payment and order updates.
                </p>
            </div>

            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">

                ← Dashboard

            </a>

        </div>


        {{-- Notifications --}}
        @forelse($notifications as $notification)

            <div class="card shadow-sm mb-3
                    {{ $notification->read_at ? '' : 'border-primary' }}">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            {{-- Notification Message --}}
                            <h5 class="mb-2">

                                🔔
                                {{ $notification->data['message']
                ?? 'You have a new notification.' }}

                            </h5>


                            {{-- Order --}}
                            @if(isset($notification->data['order_id']))

                                <p class="mb-1">

                                    <strong>Order ID:</strong>

                                    #{{ $notification->data['order_id'] }}

                                </p>

                            @endif


                            {{-- Payment --}}
                            @if(isset($notification->data['payment_id']))

                                <p class="mb-1">

                                    <strong>Payment ID:</strong>

                                    #{{ $notification->data['payment_id'] }}

                                </p>

                            @endif


                            {{-- Amount --}}
                            @if(isset($notification->data['amount']))

                                            <p class="mb-1">

                                                <strong>Amount:</strong>

                                                ₹{{ number_format(
                                    (float) $notification->data['amount'],
                                    2
                                ) }}

                                            </p>

                            @endif


                            {{-- Date --}}
                            <small class="text-muted">

                                {{ $notification->created_at->format(
                'd M Y, h:i A'
            ) }}

                            </small>

                        </div>


                        {{-- Read / Unread --}}
                        <div>

                            @if($notification->read_at)

                                <span class="badge bg-secondary">
                                    Read
                                </span>

                            @else

                                <span class="badge bg-primary">
                                    New
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        @empty

            {{-- No Notifications --}}
            <div class="card shadow-sm">

                <div class="card-body text-center py-5">

                    <div class="fs-1 mb-3">
                        🔔
                    </div>

                    <h4>
                        No Notifications Yet
                    </h4>

                    <p class="text-muted mb-0">
                        Your payment, order, and shipment updates
                        will appear here.
                    </p>

                </div>

            </div>

        @endforelse

    </div>

@endsection