@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <h4 class="card-title">Chart of Account</h4>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#exampleModalCenter">
                                    Add Chart of Account
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                            <table class="table table-bordered datatable" id="dt-coa">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Date
                                        </th>
                                        <th>
                                            Type
                                        </th>
                                        <th>
                                            Balance
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
    <!-- Modal -->
    <div class="modal fade @if (!is_null(old('name'))) show @endif" id="exampleModalCenter" tabindex="-1"
        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="forms-sample" method="post" action="{{ $store_route }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle"><b>Add Chart of Account</b></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"
                                value="{{ old('date') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="type" name="type" required>
                                <option disabled hidden selected>Choose Type</option>
                                <option value="0" @if (!is_null(old('store')) && old('type') == 0) selected @endif>
                                    Debt</option>
                                <option value="1" @if (!is_null(old('type')) && old('type') == 1) selected @endif>Credit
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="balance">Balance <span class="text-danger">*</span></label>
                            <div class='d-flex'>
                                <span class='input-group-text bg-default p-2'>Rp.</span>
                                <input type='number' class='form-control'
                                    name='balance' min='0'
                                    value='{{ old('balance')}}' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                placeholder="Description">{{ old('description') }}</textarea>
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
        @include('javascript.chart_of_account.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
