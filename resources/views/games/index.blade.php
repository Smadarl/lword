@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    @if(count($pending))
                        <h3>Pending games for me to accept</h3>
                        <ul>
                        @foreach($pending as $game)
                            <li>
                                <a href="/game/{{ $game->game_id }}/pending">
                                    {{ $game->opponent_name }}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    @endif
                    @if(count($gameList))
                        <h2>Active Games</h2>
                        <ul>
                            @foreach($gameList as $game)
                                <li class="opponent-name @if ($game->turn == Auth::id()) my-turn @endif">
                                    <a href="/game/{{ $game->game_id }}">{{ $game->opponent_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if(count($waiting))
                        <h3>Waiting for others to accept</h3>
                        <ul>
                        @foreach($waiting as $game)
                            <li>
                                <a href="/game/{{ $game->game_id }}/pending">
                                    {{ $game->opponent_name }}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('components')
    <script src="{{ asset('js/game-list.js') }}" defer></script>
@endsection