@extends('physical-possession.layouts.user')

@section('page-title', 'Profile')

@section('content')
<div class="row g-2">
    <div class="col-md-6">
        <div class="pp-panel">
            <div class="pp-panel-head">Personal Information</div>
            <div class="pp-panel-body">
                <div class="pp-detail-grid">
                    <div><div class="label">Name</div>{{ $profile['name'] }}</div>
                    <div><div class="label">Father Name</div>{{ $profile['father_name'] }}</div>
                    <div><div class="label">Mobile</div>{{ $profile['mobile'] }}</div>
                    <div><div class="label">District</div>{{ $profile['district'] }}</div>
                    <div><div class="label">Category</div>{{ $profile['category'] }}</div>
                    <div class="col-span-2"><div class="label">Address</div>{{ $profile['address'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="pp-panel">
            <div class="pp-panel-head">Registration Details</div>
            <div class="pp-panel-body">
                <p class="small mb-2">{{ $profile['registration_details'] }}</p>
                @if($profile['application_no'])<p class="small mb-1"><strong>Application No:</strong> {{ $profile['application_no'] }}</p>@endif
                @if($profile['ppp_id'])<p class="small mb-0"><strong>PPP ID:</strong> {{ $profile['ppp_id'] }}</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection
