<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Enterprise Commerce' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-dark bg-dark">

        <div class="container">

            {{-- Logo --}}
            <a class="navbar-brand" href="#">
                Enterprise Commerce
            </a>


            <div class="d-flex align-items-center gap-2">

                @auth

                    {{-- ================================= --}}
                    {{-- CUSTOMER NAVIGATION --}}
                    {{-- ================================= --}}

                    @if(auth()->user()->isCustomer())

                        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-light btn-sm">
                            🏠 Dashboard
                        </a>

                        <a href="{{ route('customer.products') }}" class="btn btn-outline-light btn-sm">
                            🛍 Products
                        </a>

                        <a href="{{ route('customer.cart') }}" class="btn btn-outline-light btn-sm">
                            🛒 My Cart
                        </a>

                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-light btn-sm">
                            📦 My Orders
                        </a>

                        <a href="{{ route('customer.notifications') }}" class="btn btn-outline-light btn-sm">
                            🔔
                        </a>


                        {{-- ================================= --}}
                        {{-- WAREHOUSE NAVIGATION --}}
                        {{-- ================================= --}}

                    @elseif(auth()->user()->isWarehouse())

                        <a href="{{ route('warehouse.dashboard') }}" class="btn btn-outline-light btn-sm">
                            🏠 Dashboard
                        </a>

                        <a href="{{ route('warehouse.inventory') }}" class="btn btn-outline-light btn-sm">
                            📦 Inventory
                        </a>

                        <a href="/warehouse/orders" class="btn btn-outline-light btn-sm">
                            🚚 Orders to Fulfill
                        </a>


                        {{-- ================================= --}}
                        {{-- ADMIN NAVIGATION --}}
                        {{-- ================================= --}}

                    @elseif(auth()->user()->isAdmin())

                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                            🏠 Dashboard
                        </a>

                        <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm">
                            📦 Products
                        </a>

                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-light btn-sm">
                            Inventory
                        </a>

                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-light btn-sm">
                            Orders
                        </a>


                        {{-- ================================= --}}
                        {{-- MANAGER NAVIGATION --}}
                        {{-- ================================= --}}

                    @elseif(auth()->user()->isManager())

                        <a href="/manager/dashboard" class="btn btn-outline-light btn-sm">
                            🏠 Dashboard
                        </a>

                        <a href="/orders" class="btn btn-outline-light btn-sm">
                            📦 Orders
                        </a>

                        <a href="/inventory" class="btn btn-outline-light btn-sm">
                            📊 Inventory
                        </a>

                    @endif


                    {{-- ================================= --}}
                    {{-- LOGOUT --}}
                    {{-- ================================= --}}

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">

                        @csrf

                        <button type="submit" class="btn btn-danger btn-sm">
                            🚪 Logout
                        </button>

                    </form>

                @endauth

            </div>

        </div>

    </nav>


    {{-- Main Content --}}
    <main class="container py-4">

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


        {{-- Validation Errors --}}
        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        @yield('content')

    </main>


    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>