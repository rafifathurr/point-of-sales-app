@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Detail Supplier #{{ $supplier->id }}</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $supplier->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $supplier->phone }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Location Address</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $supplier->address ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $supplier->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($supplier->updated_at)) }}
                            </div>
                        </div>
                        <div class="float-right">
                            <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-danger">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.supplier.script')
    @endpush
@endsection
