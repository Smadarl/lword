@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <a href="{{ route('games') }}">Games List</a>
                @if($game->started_by == Auth::id())
                    <h2>Game waiting on {{ $game->opponent_name }}</h2>
                    <div class="card-header">
                        <generic-form posturl="/api/game/{{ $game->game_id }}/cancel" success="gameCanceled">
                            Game created at {{ $game->created_at }}
                            <br/>
                            <button class="button is-primary">Cancel</button>
                        </generic-form>
                    </div>
                @else
                    <h2>Game started by {{ $game->opponent_name }}</h2>
                    <div class="card-header">
                        Maximum word length: {{ $game->max_length }}<br/>
                        Maximum duplicate letters: {{ $game->max_recurrance }}<br/>
                        <generic-form posturl="/api/game/{{ $game->game_id }}/accept" success="submitSuccess">
                            <choose-word></choose-word>
                            <button class="button is-primary">Accept Game</button>
                        </generic-form>
                    </div>
                @endif
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('components')
    <script src="{{ asset('js/pending.js') }}" defer></script>
@endsection