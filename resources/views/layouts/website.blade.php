@extends('marketing.layout')

@section('title', isset($pageTitle) ? $pageTitle : 'Asylon')

@section('content')
    <main class="website-page">
        @yield('page-content')
    </main>
@endsection
