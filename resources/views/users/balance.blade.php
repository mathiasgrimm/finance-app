@extends('layouts.app')

@section('content')
    <user-balance :user-id="{{ json_encode(auth()->user()->id) }}"></user-balance>
@endsection
