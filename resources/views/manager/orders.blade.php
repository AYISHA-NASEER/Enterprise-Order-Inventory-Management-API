@extends('layouts.app')

@section('content')

    <div class="container">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1>📦 All Orders</h1>

                <p class="text-muted mb-0">
                    View all customer orders
                </p>
            </div>

            <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary">

                ← Dashboard

            </a>

        </div>


        {{-- Orders Table --}}
        <div class="card shadow-sm">

            <div class="card-body">

                @if($orders->count() > 0)

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($orders as $order)

                                    <tr>

                                        {{-- Order ID --}}
                                        <td>
                                            #{{ $order->id }}
                                        </td>


                                        {{-- Order Number --}}
                                        <td>
                                            {{ $order->order_number ?? 'N/A' }}
                                        </td>


                                        {{-- Customer --}}
                                        <td>

                                            @if($order->user)

                                                <strong>
                                                    {{ $order->user->name }}
                                                </strong>

                                                <br>

                                                <small class="text-muted">
                                                    {{ $order->user->email }}
                                                </small>

                                            @else

                                                <span class="text-muted">
                                                    Unknown customer
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Total --}}
                                        <td>

                                            ₹{{ number_format($order->total_amount, 2) }}

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @php
                                                $status = strtolower((string) $order->status);
                                            @endphp

                                            @if($status === 'completed')

                                                <span class="badge bg-success">
                                                    Completed
                                                </span>

                                            @elseif($status === 'pending')

                                                <span class="badge bg-warning text-dark">
                                                    Pending
                                                </span>

                                            @elseif($status === 'cancelled')

                                                <span class="badge bg-danger">
                                                    Cancelled
                                                </span>

                                            @else

                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($order->status) }}
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Date --}}
                                        <td>

                                            {{ $order->created_at->format('d M Y, h:i A') }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- Pagination --}}
                    @if($orders->hasPages())

                        <div class="mt-4">

                            <div class="d-flex justify-content-center align-items-center gap-3">

                                @if($orders->onFirstPage())

                                    <button class="btn btn-outline-secondary" disabled>
                                        Previous
                                    </button>

                                @else

                                    <a href="{{ $orders->previousPageUrl() }}" class="btn btn-outline-primary">

                                        Previous

                                    </a>

                                @endif


                                <span>

                                    Page {{ $orders->currentPage() }}
                                    of {{ $orders->lastPage() }}

                                </span>


                                @if($orders->hasMorePages())

                                    <a href="{{ $orders->nextPageUrl() }}" class="btn btn-outline-primary">

                                        Next

                                    </a>

                                @else

                                    <button class="btn btn-outline-secondary" disabled>
                                        Next
                                    </button>

                                @endif

                            </div>

                        </div>

                    @endif

                @else

                    <div class="alert alert-info mb-0">

                        No orders found.

                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection