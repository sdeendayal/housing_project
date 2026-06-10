@extends('physical-possession.layouts.app')

@section('title', 'Physical Possession Management System')

@section('content')
<section class="pp-hero d-flex align-items-center py-4">
    <div class="pp-floating-shape" style="width:200px;height:200px;top:5%;left:5%;"></div>
    <div class="pp-floating-shape" style="width:150px;height:150px;bottom:10%;right:8%;animation-delay:2s;"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7 text-white text-center">

                {{-- CM Banner Photo - Center --}}
                <div class="mb-3">
                    <img src="{{ asset('images/physical-possession/cm-banner-photo.png') }}" alt="Hon'ble Chief Minister of Haryana"
                         class="pp-cm-banner-photo mx-auto mb-2">
                    <p class="fw-bold mb-0">Sh. Nayab Singh Saini</p>
                    <p class="small opacity-75 mb-0">Hon'ble Chief Minister of Haryana</p>
                </div>

                {{-- Scheme details below CM --}}
                <span class="pp-new-badge mb-3 d-inline-flex">
                    🔥 NEW - Physical Possession Application Portal
                </span>

                <h1 class="h2 fw-bold mb-2">
                    Physical Possession Management System
                </h1>

                <p class="small opacity-90 mb-3 mx-auto" style="max-width:520px;">
                    Apply online for physical possession, upload required documents,
                    track application status, and receive approval updates digitally.
                </p>

                <p class="small mb-3 px-3 py-2 rounded-3 mx-auto" style="max-width:520px;background:rgba(255,255,255,0.1);border-left:3px solid #f59e0b;">
                    <i class="bi bi-megaphone-fill me-1 text-warning"></i>
                    <strong>New Scheme Launched!</strong> Apply from home, upload documents and track your application online.
                </p>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('citizen.login') }}" class="btn pp-btn-primary">
                        <i class="bi bi-file-earmark-plus me-1"></i> User Login / Apply
                    </a>
                    <a href="{{ route('pp.department.login') }}" class="btn pp-btn-outline">
                        <i class="bi bi-shield-lock me-1"></i> Department Officer Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-4 bg-white">
    <div class="container">
        <div class="text-center mb-3">
            <h2 class="h5 fw-bold">How It Works</h2>
            <p class="text-muted small mb-0">4 simple steps to apply</p>
        </div>
        <div class="row g-2">
            @foreach([
                ['icon'=>'bi-person-check','title'=>'Login','desc'=>'Login with registered mobile & OTP'],
                ['icon'=>'bi-download','title'=>'Download Form','desc'=>'Download pre-filled form & sign'],
                ['icon'=>'bi-upload','title'=>'Upload Docs','desc'=>'Upload required documents online'],
                ['icon'=>'bi-receipt','title'=>'Get Slip','desc'=>'Download acknowledgement slip'],
            ] as $i => $step)
            <div class="col-6 col-lg-3">
                <div class="pp-panel text-center h-100">
                    <div class="pp-panel-body py-3">
                        <span class="badge bg-primary mb-2">{{ $i + 1 }}</span>
                        <i class="bi {{ $step['icon'] }} d-block text-primary mb-1"></i>
                        <h6 class="fw-bold small mb-1">{{ $step['title'] }}</h6>
                        <p class="text-muted mb-0" style="font-size:0.75rem;">{{ $step['desc'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<footer class="py-3 text-center text-muted border-top small">
    <p class="mb-0">&copy; {{ date('Y') }} Physical Possession Management System | Govt. Housing Portal</p>
</footer>
@endsection

@push('styles')
<style>
.pp-hero { min-height: auto; padding: 2rem 0; }
.pp-cm-banner-photo {
    max-width: 320px;
    width: 100%;
    height: auto;
    border-radius: 10px;
    background: #fff;
    padding: 6px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
}
@media (min-width: 768px) {
    .pp-cm-banner-photo { max-width: 380px; }
}
</style>
@endpush
