@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title">{{ $title }}</h1>
                    <p class="lead">Welcome to our application!</p>
                </div>
            </div>
        </div>
    </div>
@endsection
