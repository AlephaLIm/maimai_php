@extends('metadata', ['title' => $title, 'description' => $description])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/register.css') }}" >  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" >
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,1,0" >  
@endsection

@section('body')
<main>
    <div class='bg-limit' style="background: linear-gradient(rgba(0, 0, 0, 0.40), rgba(0, 0, 0, 0.40)), url({{ asset('images/nav_bg/bg_img.jpg') }}), no-repeat;">
        <div class='login_wrapper'>
            <div class="title">
                <h1>Register</h1>
                <p>Create account to view profile</p>
            </div>
            
            <form class="login-form" method="POST" action="/users">
                @csrf
                <div class="input-box">             
                    <label for="friendcode" class="label-input"> Friendcode </label>
                    <span class="material-symbols-outlined icons">pin</span>
                    <input type="text" id="friendcode" class="input-field" name="friendcode" placeholder="Enter friendcode" >
                
                    @error('friendcode')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>

                <div class="input-box">
                    <label for="email" class="label-input"> Email </label>
                    <span class="material-symbols-outlined icons">mail</span>
                    <input type="email" id="email" class="input-field" name="email" placeholder="Enter email" >
                
                    @error('email')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>
                
                <div class="input-box">
                    <label for="password" class="label-input"> Password </label>
                    <span class="material-symbols-outlined icons">lock</span>
                    <input type="password" id="password" class="input-field" name="password" placeholder="Enter password" >
                
                    @error('password')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>
                
                <div class="input-box">
                    <label for="password_confirmation" class="label-input"> Password Confirmation </label>
                    <span class="material-symbols-outlined icons">lock</span>
                    <input type="password" id="password_confirmation" class="input-field" name="password_confirmation" placeholder="Confirm password" >
                
                    @error('password_confirmation')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>
                
                <div class="submit-button-container">
                    <div class="shadow-wrapper">
                        <button type="submit" class="submit-button">
                        Sign Up
                        </button>
                    </div>
                </div>
                
                <div class="redirect">
                    <p>
                    Already have an account?
                    <a href="/login" class="redirect">Login</a>
                    </p>
                </div>

                <div class="guest">
                    <p>
                    Otherwise,
                    <a href="/" class="guest">Continue as Guest</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection