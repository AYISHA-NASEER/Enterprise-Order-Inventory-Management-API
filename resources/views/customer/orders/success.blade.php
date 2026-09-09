@extends('layouts.app')

@section('content')

    <div class="container py-5">

        <div class="text-center mb-5">

            <div class="display-1 mb-3">
                ✅
            </div>

            <h1 class="text-success">
                Order Created Successfully!
            </h1>

            <p class="text-muted">
                Your order has been created and is waiting for payment.
            </p>

        </div>

        <div class="row justify-content-center">

            <div class="col-md-7 col-lg-6">

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <h3 class="text-center mb-4">
                            Order Summary
                        </h3>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Order Number</span>

                            <strong>
                                {{ $order->order_number }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Status</span>

                            <span class="badge bg-warning text-dark">
                                {{ $order->status }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <span>Total</span>

                            <strong class="text-success fs-4">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </strong>
                        </div>

                        <hr>

                        <div class="text-center mt-4">

                            <a href="{{ url('/payment-test/' . $order->payments->first()->id) }}"
                                class="btn btn-success btn-lg px-5">
                                💳 Pay Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection