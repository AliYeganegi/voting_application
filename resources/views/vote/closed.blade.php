@extends('layouts.app')
@section('content')
<div class="container text-center py-5">
    <div class="alert alert-warning">رأی‌گیری بسته است</div>
    <p class="lead">متأسفیم، در حال حاضر امکان رأی‌دهی وجود ندارد.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">بازگشت به خانه</a>
</div>
@endsection
