@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body p-5">
                        <h4 class="card-title">Add User</h4>
                        <form class="forms-sample" method="post" action="{{ route('user.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Username" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                    value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="roles">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="roles" name="roles" required>
                                    <option disabled hidden selected>Choose Role</option>
                                    @foreach ($roles as $role)
                                        @if (!is_null(old('roles')) && old('roles') == $role->name)
                                            <option value="{{ $role->name }}" selected>{{ $role->name }}</option>
                                        @else
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <label for="re_password">Re Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="re_password" name="re_password"
                                    placeholder="Re Password" required>
                            </div>
                            <div class="float-right">
                                <a href="{{ route('user.index') }}" class="btn btn-sm btn-danger">
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
        @include('javascript.user.script')
    @endpush
@endsection
