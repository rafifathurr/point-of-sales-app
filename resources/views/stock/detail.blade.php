@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Detail {{ $title }} #{{ $stock->id }}</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Product</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $stock->productSize->product->name }} - {{ $stock->productSize->size }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Date</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d M Y', strtotime($stock->date)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Qty</label>
                            <div class="col-sm-9 col-form-label">
                                @if ($stock->type == 0)
                                    <span class="text-success">+ {{ $stock->qty }} Pcs</span>
                                @elseif($stock->type == 1)
                                    <span class="text-danger">- {{ $stock->qty }} Pcs</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $stock->description ?? '-' }}
                            </div>
                        </div>
                        <div class="float-right">
                            <a href="{{ $index_route }}" class="btn btn-sm btn-danger">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.stock.script')
    @endpush
@endsection
