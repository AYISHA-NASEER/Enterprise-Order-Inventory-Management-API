@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h4 class="mb-0">✏️ Edit Category</h4>
                </div>

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following:</strong>

                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">

                        @csrf
                        @method('PUT')

                        <div class="mb-3">

                            <label for="name" class="form-label">
                                Category Name
                            </label>

                            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                                class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label for="slug" class="form-label">
                                Slug
                            </label>

                            <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
                                class="form-control" required>

                            <small class="text-muted">
                                Use lowercase letters and hyphens.
                            </small>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a href="{{ route('admin.categories') }}" class="btn btn-secondary">
                                ← Back
                            </a>

                            <button type="submit" class="btn btn-primary">
                                💾 Update Category
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection