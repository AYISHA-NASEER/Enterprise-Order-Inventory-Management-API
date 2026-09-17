@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1>Supplier Sync</h1>

                <p class="text-muted mb-0">
                    Supplier synchronization status
                </p>

            </div>

            <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary">

                ← Dashboard

            </a>

        </div>


        <div class="card shadow-sm">

            <div class="card-body">

                <h4 class="mb-4">
                    Supplier Information
                </h4>

                <div class="row mb-3">

                    <div class="col-md-4">
                        <strong>Supplier</strong>
                    </div>

                    <div class="col-md-8">
                        {{ $supplier }}
                    </div>

                </div>


                <div class="row mb-3">

                    <div class="col-md-4">
                        <strong>Status</strong>
                    </div>

                    <div class="col-md-8">

                        <span class="badge bg-success">
                            {{ $status }}
                        </span>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-4">
                        <strong>Synchronization</strong>
                    </div>

                    <div class="col-md-8">
                        {{ $message }}
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection