@extends('layouts.main')
@section('section')
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center">
                                <img src="{{ asset('images/renata_label_icon.png') }}" style="width:60%" alt="logo">
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            <form class="pt-3" method="post" action="{{ route('authenticate') }}">
                                @csrf
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control form-control-lg @error('username') is-invalid @enderror"
                                            name="username" placeholder="Email or Username" value="{{ old('username') }}"
                                            required>
                                        <span class="input-group-text">
                                            <i class="mdi mdi-email"></i>
                                        </span>
                                    </div>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-lg" id="password" name="password"
                                            placeholder="Password" value="{{ old('password') }}" required>
                                        <span class="input-group-text" id="togglePassword" onclick="showPassword()">
                                            <i class="mdi mdi-eye" id="eye-icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn font-weight-bold">
                                        SIGN IN
                                    </button>
                                </div>
                                <div class="form-check form-check-flat form-check-primary mt-4">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="remember">
                                        Remember me
                                        <i class="input-helper"></i>
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    @push('javascript-bottom')
        @include('javascript.auth.script')
    @endpush
@endsection
