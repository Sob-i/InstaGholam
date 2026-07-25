@extends('layouts.front.profile.main')

@section('content')
<div class="main">
    <!-- Back Button -->
    <a href="{{route('profile',$user->username)}}" class="back-link">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Back to profile
    </a>

    <h1 class="edit-title">Edit Password</h1>

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

    <form class="edit-form" method="POST" action="{{route('profile.edit.password',$user->username)}}">
        @csrf
        @method('PUT')
        <!-- Avatar Section -->
        <div class="edit-section">
            <div class="edit-avatar-row">
                <div class="profile-av-ring">
                    @if($user->avatar != null)
                        <img src="{{ asset('users/avatar/'.$user->avatar) }}" class="profile-av">
                    @else
                        <div class="profile-av"></div>
                    @endif
                </div>
                <div>
                    <span class="edit-username">{{ $user->username }}</span>
                </div>
            </div>
        </div>

        <!-- password -->
        <div class="edit-section">
            <label class="edit-label">currentPassword</label>
            <input type="password" name="current_pass" class="edit-input" placeholder="...........">
        </div>

        <div class="edit-section">
            <label class="edit-label">NewPassword</label>
            <input type="password" name="password" class="edit-input" placeholder="...........">
        </div>

        <div class="edit-section">
            <label class="edit-label">NewPasswordConfirm</label>
            <input type="password" name="password_confirmation" class="edit-input" placeholder="...........">
        </div>

        <!-- Submit -->
        <div class="edit-actions">
            <button type="submit" class="btn-submit">Submit</button>
        </div>
    </form>
</div>
@endsection
