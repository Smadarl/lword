@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <playerprofile>
                </playerprofile>
            </div>
        </div>
    </div>
</div>
@endsection

@section('components')
    <script src="{{ asset('js/profile.js') }}" defer></script>
@endsection