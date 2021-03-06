@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <!-- <rob-test-box header="Test component">
                    This is just a little test
                </rob-test-box> -->

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="games">Current games</a>
                    <br/>
                    <a href="game/create">New Game</a>
                    <br/>
                    <a href="connect/create">New Connections Game</a>
                    <br/>
                    <a href="user/friends">Friends list</a>
                    <br/>
                    <!-- <friends></friends> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('components')
    <script src="{{ asset('js/friends.js') }}" defer></script>
@endsection