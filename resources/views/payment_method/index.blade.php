@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <h4 class="card-title">Payment Method</h4>
                            </div>
                            @if ($can_create)
                                <div class="p-2">
                                    <a href="{{ route('payment-method.create') }}" class="btn btn-sm btn-primary">
                                        Add Payment Method
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                            <table class="table table-bordered datatable" id="dt-payment-method">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.payment_method.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
