@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Edit Payment Method #{{ $payment_method->id }}</h4>
                        <form class="forms-sample" method="post"
                            action="{{ route('payment-method.update', ['id' => $payment_method->id]) }}">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ $payment_method->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                    placeholder="Description">{{ $payment_method->description }}</textarea>
                            </div>
                            <div class="float-right">
                                <a href="{{ route('payment-method.index') }}" class="btn btn-sm btn-danger">
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
        @include('javascript.payment_method.script')
    @endpush
@endsection
