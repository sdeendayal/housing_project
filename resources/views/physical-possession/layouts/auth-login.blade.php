@extends('physical-possession.layouts.app')

@section('bodyClass', 'pp-body-auth')

@php
    $loginType = $loginType ?? 'user';
    $isOfficer = in_array($loginType, ['officer', 'dtp']);
    
    if ($loginType === 'dtp') {
        $bgImage = asset('images/citizen-login/dtp_bg.jpg');
    } else {
        $bgImage = asset('images/citizen-login/mmsay_30gaj_house.jpg');
    }
@endphp

@section('content')
<div class="pp-auth-page">
    <div class="pp-auth-bg" aria-hidden="true">
        <img src="{{ $bgImage }}" alt="">
    </div>

    <div class="pp-auth-inner">
        <div class="pp-auth-card {{ $isOfficer ? 'pp-auth-card--officer' : '' }}">
            <header class="pp-auth-card__head">
                <div class="pp-auth-card__logo">
                    <img src="{{ asset('Haryana_emblem.png') }}" alt="Government of Haryana">
                </div>
                <p class="pp-auth-card__dept mb-0">Government of Haryana</p>
                <p class="pp-auth-card__scheme mb-0">Housing For All — Physical Possession Portal</p>
                @unless($isOfficer)
                    <span class="pp-auth-chip">New Portal</span>
                @endunless
                <h1 class="pp-auth-card__title">
                    @yield('authHeading', $isOfficer ? 'Officer Login' : 'User Login')
                </h1>
                <p class="pp-auth-card__hint mb-0">
                    @yield('authSubheading', $isOfficer ? 'Department officer mobile & captcha to receive OTP' : 'Mobile number & captcha to receive OTP')
                </p>
            </header>

            <div class="pp-auth-card__body">
                @yield('loginForm')
            </div>

            <footer class="pp-auth-card__foot">
                <div class="pp-auth-features">
                    <span><i class="bi bi-shield-check"></i> Secure</span>
                    <span><i class="bi bi-cloud-upload"></i> Upload</span>
                    <span><i class="bi bi-graph-up"></i> Track</span>
                </div>
                <a href="{{ route('pp.landing') }}" class="pp-auth-back">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </footer>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush
