@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Edit Supplier #{{ $supplier->id }}</h4>
                        <form class="forms-sample" method="post"
                            action="{{ route('supplier.update', ['id' => $supplier->id]) }}">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ $supplier->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="Phone Number" value="{{ $supplier->phone }}" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Location Address</label>
                                <textarea class="form-control" name="address" id="address" cols="10" rows="3"
                                    placeholder="Location Address">{{ $supplier->address }}</textarea>
                            </div>
                            <div class="float-right">
                                <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-danger">
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
        @include('javascript.supplier.script')
    @endpush
@endsection
