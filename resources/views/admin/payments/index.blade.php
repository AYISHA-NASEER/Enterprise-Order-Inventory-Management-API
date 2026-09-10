@extends('layouts.app')

@section('content')

    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Payments</h2>
                <p class="text-muted mb-0">
                    View all customer payment transactions
                </p>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">
                ← Dashboard
            </a>
        </div>


        {{-- Summary Cards --}}
        <div class="row mb-4">

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Payments</h6>
                        <h3>{{ $payments->total() }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Current Page</h6>
                        <h3>{{ $payments->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Per Page</h6>
                        <h3>{{ $payments->perPage() }}</h3>
                    </div>
                </div>
            </div>

        </div>


        {{-- Payments Table --}}
        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                <strong>Payment Transactions</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Payment ID</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Razorpay Order ID</th>
                                <th>Razorpay Payment ID</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($payments as $payment)

                                <tr>

                                    {{-- Payment ID --}}
                                    <td>
                                        <strong>#{{ $payment->id }}</strong>
                                    </td>


                                    {{-- Order --}}
                                    <td>
                                        @if($payment->order)
                                            #{{ $payment->order->id }}

                                            @if($payment->order->order_number)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $payment->order->order_number }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">
                                                No order
                                            </span>
                                        @endif
                                    </td>


                                    {{-- Customer --}}
                                    <td>
                                        @if($payment->order?->user)

                                            <strong>
                                                {{ $payment->order->user->name }}
                                            </strong>

                                            <br>

                                            <small class="text-muted">
                                                {{ $payment->order->user->email }}
                                            </small>

                                        @else

                                            <span class="text-muted">
                                                Unknown customer
                                            </span>

                                        @endif
                                    </td>


                                    {{-- Amount --}}
                                    <td>
                                        <strong>
                                            ₹{{ number_format($payment->amount, 2) }}
                                        </strong>
                                    </td>


                                    {{-- Razorpay Order --}}
                                    <td>
                                        <small>
                                            {{ $payment->razorpay_order_id ?? '—' }}
                                        </small>
                                    </td>


                                    {{-- Razorpay Payment --}}
                                    <td>
                                        <small>
                                            {{ $payment->razorpay_payment_id ?? '—' }}
                                        </small>
                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($payment->status === 'paid')

                                            <span class="badge bg-success">
                                                Paid
                                            </span>

                                        @elseif($payment->status === 'failed')

                                            <span class="badge bg-danger">
                                                Failed
                                            </span>

                                        @elseif($payment->status === 'created')

                                            <span class="badge bg-warning text-dark">
                                                Created
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                {{ ucfirst($payment->status) }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Created --}}
                                    <td>
                                        <small>
                                            {{ $payment->created_at->format('d M Y H:i') }}
                                        </small>
                                    </td>


                                    {{-- Action --}}
                                    <td>

                                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="9" class="text-center py-4">

                                        <h5>No payments found</h5>

                                        <p class="text-muted mb-0">
                                            There are currently no payment records.
                                        </p>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-4">

            <div class="text-muted">
                Showing
                {{ $payments->firstItem() ?? 0 }}
                -
                {{ $payments->lastItem() ?? 0 }}
                of
                {{ $payments->total() }}
                payments
            </div>

            <div>
                {{ $payments->links() }}
            </div>

        </div>

    </div>

@endsection