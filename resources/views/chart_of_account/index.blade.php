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
                                    data-target="#create">
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
                                            Account Number
                                        </th>
                                        <th>
                                            Name
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
    <!-- Modal Create -->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="createTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="forms-sample" id="form-store" method="post" action="{{ $store_route }}"
                    enctype="multipart/form-data">
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
                            <label for="account_number">Account Number <span class="text-danger">*</span></label>
                            <select class="form-control" id="account_number" name="account_number" required>
                                <option disabled hidden selected>Choose Account Number</option>
                                @foreach ($account_number_collection as $account_number)
                                    <option value="{{ $account_number->id }}"
                                        @if (!is_null(old('account_number')) && old('account_number') == $account_number->id) selected @endif>
                                        {{ $account_number->account_number . ' - ' . $account_number->name }}</option>
                                @endforeach
                            </select>
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
                                <input type='number' class='form-control' name='balance' min='0'
                                    value='{{ old('balance') }}' required>
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
    <!-- Modal Show -->
    <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="createTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle"><b>Detail Chart of Account #<span
                                id="id_show"></span></b></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name_show" name="name" placeholder="Name"
                            disabled>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" name="date" id="date_show" disabled>
                    </div>
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <select class="form-control" id="account_number_id_show" name="account_number" disabled>
                            <option disabled hidden selected>Choose Account Number</option>
                            @foreach ($account_number_collection as $account_number)
                                <option value="{{ $account_number->id }}">
                                    {{ $account_number->account_number . ' - ' . $account_number->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-control" id="type_show" name="type" disabled>
                            <option disabled hidden selected>Choose Type</option>
                            <option value="0">
                                Debt</option>
                            <option value="1">Credit
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="balance">Balance</label>
                        <div class='d-flex'>
                            <span class='input-group-text bg-default p-2'>Rp.</span>
                            <input type='number' class='form-control' id="balance_show" name='balance' min='0'
                                disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description_show" cols="10" rows="3"
                            placeholder="Description" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="updated_at_show">Updated By</label>
                        <input type="text" class="form-control" id="updated_by_show" disabled>
                    </div>
                    <div class="form-group">
                        <label for="updated_at_show">Updated At</label>
                        <input type="text" class="form-control" id="updated_at_show" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Back</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit -->
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="createTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="forms-sample" id="form-edit" method="post" action=""
                    enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle"><b>Edit Chart of Account #<span
                                    id="id_edit"></span></b></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" class="form-control" id="url_edit" value="{{ url('coa/') }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name_edit" name="name" placeholder="Name"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" id="date_edit"
                                max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="account_number">Account Number <span class="text-danger">*</span></label>
                            <select class="form-control" id="account_number_id_edit" name="account_number" required>
                                <option disabled hidden selected>Choose Account Number</option>
                                @foreach ($account_number_collection as $account_number)
                                    <option value="{{ $account_number->id }}">
                                        {{ $account_number->account_number . ' - ' . $account_number->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="type_edit" name="type" required>
                                <option disabled hidden selected>Choose Type</option>
                                <option value="0">
                                    Debt</option>
                                <option value="1">Credit
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="balance">Balance <span class="text-danger">*</span></label>
                            <div class='d-flex'>
                                <span class='input-group-text bg-default p-2'>Rp.</span>
                                <input type='number' class='form-control' id="balance_edit" name='balance'
                                    min='0' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description_edit" cols="10" rows="3"
                                placeholder="Description">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="updated_at_edit">Updated By</label>
                            <input type="text" class="form-control" id="updated_by_edit" disabled>
                        </div>
                        <div class="form-group">
                            <label for="updated_at_edit">Updated At</label>
                            <input type="text" class="form-control" id="updated_at_edit" disabled>
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
