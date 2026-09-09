@extends('layouts.app')

@section('content')

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-md-7 col-lg-6">

                {{-- Payment Card --}}
                <div class="card shadow-sm border-0">

                    {{-- Header --}}
                    <div class="card-header bg-dark text-white text-center py-4">
                        <h3 class="mb-1">💳 Secure Payment</h3>

                        <small class="text-light">
                            Enterprise Commerce
                        </small>
                    </div>

                    <div class="card-body p-4">

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

                        {{-- Order Information --}}
                        <div class="mb-4">

                            <h5 class="mb-3">
                                Order Details
                            </h5>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    Order Number
                                </span>

                                <strong>
                                    {{ $payment->order->order_number }}
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    Order Status
                                </span>

                                @if($payment->order->status === 'pending')

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @elseif($payment->order->status === 'paid')

                                    <span class="badge bg-success">
                                        Paid
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ $payment->order->status }}
                                    </span>

                                @endif

                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-muted">
                                    Payment Status
                                </span>

                                <strong>
                                    {{ ucfirst($payment->status) }}
                                </strong>
                            </div>

                        </div>

                        <hr>

                        {{-- Amount --}}
                        <div class="text-center my-4">

                            <small class="text-muted">
                                Amount to Pay
                            </small>

                            <h1 class="text-success fw-bold mt-2">
                                ₹{{ number_format((float) $payment->amount, 2) }}
                            </h1>

                        </div>

                        {{-- Payment Information --}}
                        <div class="alert alert-info">

                            <strong>🔒 Test Payment</strong>

                            <p class="mb-0 mt-1">
                                This is Razorpay Test Mode.
                                No real money will be charged.
                            </p>

                        </div>

                        {{-- Already Paid --}}
                        @if($payment->status === 'paid')

                            <div class="alert alert-success text-center">
                                <h5>✓ Payment Completed</h5>

                                <p class="mb-0">
                                    This order has already been paid.
                                </p>
                            </div>

                            <div class="text-center">

                                <a href="{{ route('customer.orders') }}" class="btn btn-primary">
                                    ← Back to My Orders
                                </a>

                            </div>

                        @else

                            {{-- Pay Button --}}
                            <div class="d-grid">

                                <button type="button" id="payButton" class="btn btn-success btn-lg">
                                    💳 Pay ₹{{ number_format((float) $payment->amount, 2) }}
                                </button>

                            </div>

                            <p class="text-center text-muted small mt-3">
                                You will be redirected to Razorpay's secure
                                payment window.
                            </p>

                        @endif

                    </div>

                    {{-- Footer --}}
                    <div class="card-footer text-center text-muted">

                        <small>
                            🔐 Secure payment powered by Razorpay
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Razorpay Checkout --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>

        document.getElementById('payButton')?.addEventListener('click', function () {

            const button = this;

            button.disabled = true;

            button.innerHTML = '⏳ Opening Payment...';

            const options = {

                key: "{{ config('services.razorpay.key') }}",

                amount: "{{ (int) round((float) $payment->amount * 100) }}",

                currency: "INR",

                name: "Enterprise Commerce",

                description: "Order {{ $payment->order->order_number }}",

                order_id: "{{ $payment->razorpay_order_id }}",

                handler: function (response) {

                    /*
                     * Send Razorpay response to Laravel.
                     *
                     * Laravel will verify the signature
                     * before marking the payment as paid.
                     */

                    fetch(
                        "{{ url('/api/v1/payments/' . $payment->id . '/verify') }}",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json"
                            },

                            body: JSON.stringify({

                                razorpay_order_id:
                                    response.razorpay_order_id,

                                razorpay_payment_id:
                                    response.razorpay_payment_id,

                                razorpay_signature:
                                    response.razorpay_signature

                            })
                        }
                    )
                        .then(response => response.json())

                        .then(data => {

                            if (data.message) {

                                window.location.href =
                                    "{{ route('customer.orders') }}";

                            } else {

                                alert(
                                    data.message ??
                                    "Payment verification failed."
                                );

                                button.disabled = false;

                                button.innerHTML =
                                    "💳 Pay ₹{{ number_format((float) $payment->amount, 2) }}";
                            }

                        })

                        .catch(error => {

                            console.error(error);

                            alert(
                                "Something went wrong while verifying the payment."
                            );

                            button.disabled = false;

                            button.innerHTML =
                                "💳 Pay ₹{{ number_format((float) $payment->amount, 2) }}";

                        });

                },

                modal: {

                    ondismiss: function () {

                        button.disabled = false;

                        button.innerHTML =
                            "💳 Pay ₹{{ number_format((float) $payment->amount, 2) }}";

                    }

                },

                theme: {

                    color: "#198754"

                }

            };

            const razorpay = new Razorpay(options);

            razorpay.open();

        });

    </script>

@endsection