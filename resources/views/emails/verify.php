@extends('emails.email')

@section('title', $title)

@section('content')
	<h1>{{ $title }}</h1>
	<p>{{ $message }}</p>
	<p>
		<a href="{{ $actionUrl }}" style="display: inline-block; padding: 10px 20px; color: #ffffff; background-color: #007BFF; text-decoration: none; border-radius: 5px;">
			{{ $actionText }}
		</a>
	</p>
@endsection
