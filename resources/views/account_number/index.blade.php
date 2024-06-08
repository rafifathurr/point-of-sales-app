@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <h4 class="card-title">Account Number</h4>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#create">
                                    Add Account Number
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                            <table class="table table-bordered datatable" id="dt-account-number">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Account Number
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
    <!-- Modal -->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="forms-sample" id="form-store" method="post" action="{{ $store_route }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle"><b>Add Account Number</b></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="account_number">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                placeholder="Account Number" value="{{ old('account_number') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                value="{{ old('name') }}" required>
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
    <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="show" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle"><b>Detail Account Number #<span
                                id="id_show"></span></b></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="account_number_show">Account Number</label>
                        <input type="text" class="form-control" id="account_number_show" name="account_number"
                            placeholder="Account Number" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name_show">Name</label>
                        <input type="text" class="form-control" id="name_show" name="name" placeholder="Name"
                            disabled>
                    </div>
                    <div class="form-group">
                        <label for="description_show">Description</label>
                        <textarea class="form-control" name="description" id="description_show" cols="10" rows="3"
                            placeholder="Description" disabled></textarea>
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
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="forms-sample" id="form-edit" method="post" action="" enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle"><b>Edit Account Number #<span
                                    id="id_edit"></span></b></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" class="form-control" id="url_edit" value="{{ url('account-number/') }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="account_number">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="account_number_edit" name="account_number"
                                placeholder="Account Number" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name_edit" name="name" placeholder="Name"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description_edit" cols="10" rows="3"
                                placeholder="Description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="updated_at_show">Updated At</label>
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
        @include('javascript.account_number.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
