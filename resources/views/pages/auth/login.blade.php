@extends('layouts.app')
@section('title', 'Login')
@section('style')
    {{-- @parent --}}
    <style>
        .login span {
            font-size: 12px;
            font-weight: bold;
        }

        .login #box {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px;
            transition: box-shadow .3s ease-in-out;
        }

        .login #box:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 5px 15px;
            border-radius: 10px;
            cursor: pointer;
        }

        .login #box.active {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        .login .password-area {
            position: relative;
        }

        .login .password-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
@endsection
@section('content')

    <div class="container">
        <div class="login d-flex justify-content-center align-items-center" style="min-height: 100vh">
            <div class="p-4 box" id="box">
                <h5 class="fw-bold my-4 alert alert-primary">Login</h5>
                <div id="message">
                    @if (session('success'))
                        <div class="alert alert-success fw-bold my-2"> {{ session('success') }}</div>
                    @elseif(session('danger'))
                        <div class="alert alert-danger fw-bold my-2"> {{ session('danger') }}</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mt-3 mb-2">
                        <label class="mb-2 fw-bold">Your Email</label>
                        <input style="width: 250px" class="form-control" type="text" placeholder="Email" name="email"
                            value="{{ old('email') }}">
                        @error('email')
                            <span style="color: #ff0000">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-3 mb-2">
                        <label class="mb-2 fw-bold">Your Password</label>
                        <div class="password-area">
                            <input style="width: 250px" class="form-control" id="password" type="password"
                                placeholder="Password" name="password" value="{{ old('password') }}">
                            <div class="password-eye top-50 end-10 translate-middle-y">
                                <i id="password-toggle" style="cursor: pointer" class="fas fa-eye"></i>
                            </div>
                        </div>
                        @error('password')
                            <span style="color: #ff0000">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" id="submit" class="w-100 btn btn-primary mt-3 mb-2">Login</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const passwordToggle = document.getElementById("password-toggle");
        const passwordInput = document.getElementById("password");
        passwordToggle.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.classList.remove("fa-eye");
                passwordToggle.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                passwordToggle.classList.remove("fa-eye-slash");
                passwordToggle.classList.add("fa-eye");
            }
        });
        const box = document.getElementById("box");
        const myButton = document.getElementById("submit");
        myButton.addEventListener("click", function() {
            box.classList.add("active");
        });
    </script>
@endsection
