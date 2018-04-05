@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                        <h2>Games</h2>
                        <ul>
                            @foreach($gameList as $game)
                                <li class="opponent-name @if ($game->turn == Auth::id()) my-turn @endif">
                                    <a href="/game/{{ $game->game_id }}">{{ $game->opponent_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
