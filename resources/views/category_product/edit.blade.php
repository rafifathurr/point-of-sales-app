@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Edit Category Product #{{ $category_product->id }}</h4>
                        <form class="forms-sample" method="post"
                            action="{{ route('category-product.update', ['id' => $category_product->id]) }}">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ $category_product->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                    placeholder="Description">{{ $category_product->description }}</textarea>
                            </div>
                            <div class="float-right">
                                <a href="{{ route('category-product.index') }}" class="btn btn-sm btn-danger">
                                    Back
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.category_product.script')
    @endpush
@endsection
