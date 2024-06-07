@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <h4 class="card-title">{{ $title }}</h4>
                            </div>
                            @if ($can_create)
                                <div class="p-2">
                                    <a href="{{ $create_route }}" class="btn btn-sm btn-primary">
                                        Add {{ $title }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                            <input type="hidden" id="url_destroy" value="{{ $destroy_route }}">
                            <table class="table table-bordered datatable" id="dt-stock">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Date
                                        </th>
                                        <th>
                                            Product
                                        </th>
                                        <th>
                                            Qty
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
        @include('javascript.stock.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
