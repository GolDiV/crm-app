@extends('layouts.app')

@section('title', 'Заглушка')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Раздел</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="alert alert-info">
        <strong>Это заглушка.</strong> Страница в разработке.
    </div>
@endsection
