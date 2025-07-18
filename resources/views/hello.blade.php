{{-- filepath: resources/views/hello.blade.php --}}
@extends('layouts.app')

@section('title', 'Hello')

@section('content')
    <h1>{{ $message }}</h1>
    <p>The current time is: {{ $time }}</p>
@endsection
