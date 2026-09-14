@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2>All Orders</h2>

                <p class="text-muted mb-0">
                    View and manage customer orders
                </p>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">
                ← Dashboard
            </a>

        </div>


        {{-- Orders Table --}}
        <div class="card shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-dark">

                            <tr>
                                <th>ID</th>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th width="100">Action</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse($orders as $order)

                                <tr>

                                    {{-- ID --}}
                                    <td>
                                        {{ $order->id }}
                                    </td>


                                    {{-- Order Number --}}
                                    <td>
                                        <strong>
                                            {{ $order->order_number }}
                                        </strong>
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
                                                Unknown Customer
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Total --}}
                                    <td>
                                        ₹{{ number_format((float) $order->total_amount, 2) }}
                                    </td>


                                    {{-- Status --}}
                                    <td>

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

                                    </td>


                                    {{-- Created --}}
                                    <td>
                                        {{ $order->created_at->format('d M Y, h:i A') }}
                                    </td>


                                    {{-- Action --}}
                                    <td>

                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-4">

                                        <span class="text-muted">
                                            No orders found.
                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Pagination --}}
        @if($orders->hasPages())

            <div class="mt-4 mb-4">

                <nav aria-label="Orders pagination">

                    <div class="d-flex justify-content-center align-items-center gap-3">


                        {{-- Previous --}}
                        @if($orders->onFirstPage())

                            <button class="btn btn-outline-secondary" disabled>
                                ← Previous
                            </button>

                        @else

                            <a href="{{ $orders->previousPageUrl() }}" class="btn btn-outline-primary">
                                ← Previous
                            </a>

                        @endif


                        {{-- Page Information --}}
                        <span class="text-muted">

                            Page

                            <strong>
                                {{ $orders->currentPage() }}
                            </strong>

                            of

                            <strong>
                                {{ $orders->lastPage() }}
                            </strong>

                        </span>


                        {{-- Next --}}
                        @if($orders->hasMorePages())

                            <a href="{{ $orders->nextPageUrl() }}" class="btn btn-primary">
                                Next →
                            </a>

                        @else

                            <button class="btn btn-outline-secondary" disabled>
                                Next →
                            </button>

                        @endif

                    </div>

                </nav>

            </div>

        @endif

    </div>

@endsection