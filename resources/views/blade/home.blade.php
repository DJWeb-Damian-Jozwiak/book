@extends('layouts/app.blade.php')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <x-card header="Mój nagłówek" footer="Stopka">
                <p>Card component is working</p>

                @slot('header')
                    custom header
                @endslot

                @slot('footer')
                    custom footer <p>dadasdas</p>
                @endslot

            </x-card>

            <x-button class="my-3" type="primary">click me</x-button>
            <x-alert type="success" dismissible="true">Alert component</x-alert>
        </div>
    </div>
@endsection
