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
                                        ₹{{ number_format($order->total_amount, 2) }}
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

                                        <a href="#" class="btn btn-sm btn-outline-primary">
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

            <div class="mt-4">

                {{-- Previous / Page Numbers / Next --}}
                <nav aria-label="Orders pagination">

                    <ul class="pagination justify-content-center mb-2">

                        {{-- Previous --}}
                        @if($orders->onFirstPage())

                            <li class="page-item disabled">

                                <span class="page-link">
                                    « Previous
                                </span>

                            </li>

                        @else

                            <li class="page-item">

                                <a class="page-link" href="{{ $orders->previousPageUrl() }}">
                                    « Previous
                                </a>

                            </li>

                        @endif


                        {{-- Page Numbers --}}
                        @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)

                            @if($page == $orders->currentPage())

                                <li class="page-item active">

                                    <span class="page-link">
                                        {{ $page }}
                                    </span>

                                </li>

                            @else

                                <li class="page-item">

                                    <a class="page-link" href="{{ $url }}">
                                        {{ $page }}
                                    </a>

                                </li>

                            @endif

                        @endforeach


                        {{-- Next --}}
                        @if($orders->hasMorePages())

                            <li class="page-item">

                                <a class="page-link" href="{{ $orders->nextPageUrl() }}">
                                    Next »
                                </a>

                            </li>

                        @else

                            <li class="page-item disabled">

                                <span class="page-link">
                                    Next »
                                </span>

                            </li>

                        @endif

                    </ul>

                </nav>





            </div>

        @endif

    </div>

@endsection