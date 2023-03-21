@extends('navbar', ['title' => $title, 'description' => $description, 'logo_url' => $logo_url, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/register_test.css') }}" />    
@endsection

@section('body')
    <div class='bg-limit'>
        <div class='login_wrapper'>
            <span class="title">
                <h1>Register</h1>
                <h6>Create account to view profile</h6>
            </span>
            
            <form class="login-form" method="POST" action="/users">
                @csrf
                <div class="input-box">             
                    <label for="friendcode" class="label-input"> Friendcode </label>
                    <span id="icons" class="material-symbols-outlined">pin</span>
                    <input type="text" class="input-field" name="friendcode" placeholder="Enter friendcode" />
                
                    @error('friendcode')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>

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
                
                <div class="input-box">
                    <label for="password_confirmation" class="label-input"> Password Confirmation </label>
                    <span id="icons" class="material-symbols-outlined">lock</span>
                    <input type="password" class="input-field" name="password_confirmation" placeholder="Confirm password" />
                
                    @error('password_confirmation')
                    <p class="error-msg">{{$message}}</p>
                    @enderror
                </div>
                
                <div class="submit-button-container">
                    <button type="submit" class="submit-button">
                    Sign Up
                    </button>
                </div>
                
                <div class="redirect">
                    <p>
                    Already have an account?
                    <a href="/login" class="redirect">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection