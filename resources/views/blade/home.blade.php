@extends('layouts/app.blade.php')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <x-card header="Mój nagłówek" footer="Stopka">
                <p>Card component is working</p>
                <x-button class="my-3" type="primary">click me</x-button>
                @slot('header')
                    custom header
                @endslot

                @slot('footer')
                    custom footer <p>dadasdas</p>
                    <x-alert type="success" dismissible="true">Alert component</x-alert>
                @endslot

            </x-card>



        </div>
    </div>
@endsection
