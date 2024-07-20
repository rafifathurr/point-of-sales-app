@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="d-lg-flex justify-content-between">
                            <div class="p-2">
                                <h4 class="card-title">Sales Order</h4>
                            </div>
                            @if ($can_create)
                                <div class="p-2">
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                        data-target="#import">
                                        Import Excel
                                    </button>
                                    <a href="{{ $create_route }}" class="btn btn-sm btn-primary">
                                        Add Sales Order
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                            <table class="table table-bordered datatable" id="dt-sales-order">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            No. Invoice
                                        </th>
                                        <th>
                                            Date & Time
                                        </th>
                                        <th>
                                            Purchase Type
                                        </th>
                                        <th>
                                            Payment Method
                                        </th>
                                        <th>
                                            Total Price
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
    <!-- Modal Import -->
    <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="import" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="forms-sample" id="form_import" method="post" action="{{route('sales-order.import')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle"><b>Import Excel</b></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">File Excel <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file"
                                placeholder="Account Number" accept=".xls,.xlsx" value="{{ old('file') }}" required>
                            <p class="text-danger py-1">* .xls, .xlsx</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.sales_order.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
