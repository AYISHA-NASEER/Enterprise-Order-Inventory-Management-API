@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">🚚 Shipments</h2>
                <p class="text-muted mb-0">
                    Manage customer shipments and delivery status.
                </p>
            </div>

            <a href="{{ route('warehouse.dashboard') }}" class="btn btn-secondary">
                ← Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">

                @if($shipments->count())

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Tracking Number</th>
                                    <th>Carrier</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($shipments as $shipment)

                                    <tr>

                                        <td>
                                            #{{ $shipment->id }}
                                        </td>

                                        <td>
                                            #{{ $shipment->order_id }}

                                            @if($shipment->order)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $shipment->order->order_number }}
                                                </small>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $shipment->order?->user?->name ?? 'N/A' }}
                                        </td>

                                        <td>
                                            {{ $shipment->tracking_number ?? 'Not assigned' }}
                                        </td>

                                        <td>
                                            {{ $shipment->carrier ?? 'N/A' }}
                                        </td>

                                        <td>

                                            @if($shipment->status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    Pending
                                                </span>

                                            @elseif($shipment->status === 'shipped')
                                                <span class="badge bg-primary">
                                                    Shipped
                                                </span>

                                            @elseif($shipment->status === 'delivered')
                                                <span class="badge bg-success">
                                                    Delivered
                                                </span>

                                            @else
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($shipment->status) }}
                                                </span>
                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="text-center py-5">

                        <div class="fs-1">🚚</div>

                        <h4>No Shipments Found</h4>

                        <p class="text-muted">
                            Shipments will appear here after customer payments are captured.
                        </p>

                    </div>

                @endif

            </div>
        </div>

    </div>

@endsection