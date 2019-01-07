@extends('layouts.app')

@section('content')
    <chat-component user="{{auth()->user()}}"></chat-component>   
@endsection