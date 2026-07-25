@extends('layouts.auth.main')
@section('content')
    <div class="left">
        <div class="left-inner">
            <div class="logo">InstaGholam<span>.</span></div>
            <p class="tagline">Share moments. Build your world.</p>

            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="7" stroke="#c8f04d" stroke-width="1.5"/>
                        <path d="M5 8l2 2 4-4" stroke="#c8f04d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('fail'))
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="7" stroke="#fc5c7d" stroke-width="1.5"/>
                        <path d="M8 5v4M8 11h.01" stroke="#fc5c7d" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>{{ session('fail') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="7" stroke="#fc5c7d" stroke-width="1.5"/>
                        <path d="M8 5v4M8 11h.01" stroke="#fc5c7d" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <ul style="margin:0; padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="tabs">
                <button class="tab active" onclick="switchTab('login')">Sign In</button>
                <button class="tab" onclick="switchTab('register')">Create Account</button>
            </div>

            <!-- LOGIN -->
            <form method="POST" action="{{route('admin.login')}}">
                @csrf
                <div id="login-form" class="form">
                    <div class="field">
                        <label>Email</label>
                        <input type="text" name="email" placeholder="you@example.com"/>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••"/>
                    </div>
                    <button class="btn-primary">Sign In</button>
                    <p class="terms">By signing in you agree to our <a href="#">Terms</a> & <a href="#">Privacy Policy</a></p>
                </div>
            </form>

            <!-- REGISTER -->
            <form method="POST" action="{{route('admin.signup')}}">
                @csrf
            <div id="register-form" class="form">
                <div class="field">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="alexrivera"/>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="you@example.com"/>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                        title="Must contain: 8+ characters, one uppercase, one number, one special character"
                    />
                </div>
                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••"/>
                </div>
                <button type="submit" class="btn-primary">Create Account</button>
                <p class="terms">By creating an account you agree to our <a href="#">Terms</a></p>
            </div>
            </form>
        </div>
    </div>

    <div class="right">
        <div class="phone">
            <div class="phone-notch"></div>
            <div class="phone-feed">
                <div class="phone-stories">
                    <div class="phone-story"></div>
                    <div class="phone-story"></div>
                    <div class="phone-story"></div>
                    <div class="phone-story"></div>
                </div>
                <div class="phone-card">
                    <div class="phone-card-img a"></div>
                    <div class="phone-card-meta">
                        <div class="phone-mini-av"></div>
                        <div class="phone-line"></div>
                    </div>
                </div>
                <div class="phone-card">
                    <div class="phone-card-img b"></div>
                    <div class="phone-card-meta">
                        <div class="phone-mini-av"></div>
                        <div class="phone-line"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="floating-tag t1">✦ 12k likes</div>
        <div class="floating-tag t2">📸 New Post</div>
    </div>
@endsection

