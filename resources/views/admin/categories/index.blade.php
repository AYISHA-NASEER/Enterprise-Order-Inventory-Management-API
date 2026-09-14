@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>📂 Categories</h2>

            <p class="text-muted mb-0">
                Manage product categories.
            </p>
        </div>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            ➕ Add Category
        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-body">

            <h5 class="mb-3">All Categories</h5>

            @if($categories->count() > 0)

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Created At</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($categories as $category)

                                <tr>

                                    <td>
                                        {{ $category->id }}
                                    </td>

                                    <td>
                                        {{ $category->name }}
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $category->slug }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $category->created_at->format('d M Y') }}
                                    </td>

                                    <td>

                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="btn btn-sm btn-warning">
                                            ✏️ Edit
                                        </a>

                                        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this category?')">
                                                🗑️ Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $categories->links() }}
                </div>

            @else

                <div class="alert alert-info mb-0">
                    No categories found.
                </div>

            @endif

        </div>

    </div>

@endsection