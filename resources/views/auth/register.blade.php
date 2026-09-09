<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Enterprise Commerce</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-5">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h3 class="text-center mb-2">
                        Enterprise Commerce
                    </h3>

                    <h5 class="text-center mb-4">
                        Create Account
                    </h5>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('register.submit') }}"
                        method="POST"
                    >

                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                            >

                            <small class="text-muted">
                                Minimum 8 characters
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Register
                        </button>

                    </form>

                    <div class="text-center mt-3">
                        <span class="text-muted">
                            Already have an account?
                        </span>

                        <a href="{{ route('login') }}">
                            Login
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>