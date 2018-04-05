@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h2>Game with {{ $playerGame->opponent_name }}</h2>
                <div class="card-header">

                    <h3>Letters</h3>
                    <letter-list max-count="{{ $playerGame->max_recurrance }}" inline-template>
                        <ul class="letter-list">
                            <letter v-for="(count, letter) in letters" :the-letter="letter" :the-count="count" :key="letter">
                            </letter>
                        </ul>
                    </letter-list>

                    <div style="clear: both"></div>

                    <move-list game-id="{{ $playerGame->game_id }}">
                    </move-list>
                </div>

                <guess-box game-turn="{{ $playerGame->turn }}" my-id="{{ $playerGame->player_id }}" game-id="{{ $playerGame->game_id }}" inline-template>
                    <div v-if="this.myturn" >
                        <h3>Your Turn</h3>
                         <form method="POST" action="/game/move" @submit.prevent="onSubmit">
                            <input type="text" id="guess" name="guess" class="input" v-model="guess"/>
                            <button class="button is-primary">Submit</button>
                        </form>
                    </div>
                </guess-box>

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
