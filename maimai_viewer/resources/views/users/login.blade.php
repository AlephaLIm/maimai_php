@extends('navbar', ['title' => $title, 'description' => $description, 'logo_url' => $logo_url, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}" />    
@endsection

@section('body')
    <div class='bg-limit'>
        <div class='login_wrapper'>
            <span class="title">
                <h1>Login</h1>
            </span>
            
            <form class="login-form" method="POST" action="/users/authenticate">
                @csrf
                <div class="input-box">
                    <label for="email" class="label-input"> Email </label>
                    <span id="icons" class="material-symbols-outlined">mail</span>
                    <input type="email" class="input-field" name="email" placeholder="Enter email" />
                
                    @error('email')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>
                
                <div class="input-box">
                    <label for="password" class="label-input"> Password </label>
                    <span id="icons" class="material-symbols-outlined">lock</span>
                    <input type="password" class="input-field" name="password" placeholder="Enter password" />
                
                    @error('password')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>
            
                <div class="submit-button-container">
                    <button type="submit" class="submit-button">
                    Sign In
                    </button>
                </div>
                
                <div class="redirect">
                    <p>
                    Don't have an account?
                    <a href="/register" class="redirect">Register</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection