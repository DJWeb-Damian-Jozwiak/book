@extends('layouts/app.blade.php')
@section('content')
    Hello world blade
    <x-button type="primary" disabled="true">
        Click me
        @slot('badge')
            45
        @endslot

        @push('styles')
            <style>
                button {
                    margin: 10px;
                }
            </style>
        @endpush
    </x-button>
@endsection