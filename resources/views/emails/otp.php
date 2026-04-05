@extends('emails.email')

@section('title', $title)

@section('content')
	<p class="welcome-text">{{ $message }}</p>

	<div class="otp-section">
		<span class="otp-label">{{ trans('auth.otp.label') }}</span>
		<div class="otp-code">{{ $otp }}</div>
		<p class="expiry-info">{{ trans('auth.otp.expiry', ['minutes' => config('auth.otp.expiration')]) }}</p>
	</div>
@endsection
