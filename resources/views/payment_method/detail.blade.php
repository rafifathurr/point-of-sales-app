@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Detail Payment Method #{{ $payment_method->id }}</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $payment_method->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $payment_method->description ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $payment_method->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d M Y H:i:s', strtotime($payment_method->updated_at)) }}
                            </div>
                        </div>
                        <div class="float-right">
                            <a href="{{ route('payment-method.index') }}" class="btn btn-sm btn-danger">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.payment_method.script')
    @endpush
@endsection
