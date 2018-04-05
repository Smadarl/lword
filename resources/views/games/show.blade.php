@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h2>Game with {{ $playerGame->opponent_name }}</h2>
                <div class="card-header">
                    <h3>Moves</h3>
                    <table>
                        @foreach($moves as $move)
                            <tr>
                                <td>{{ $move->guess }}</td>
                                <td>{{ $move->result }}</td>
                            </tr>
                        @endforeach
                    </table>
                    
                    <letter-list max-count="{{ $playerGame->max_recurrance }}" inline-template>
                        <div>
                            <letter v-for"letter in letters" letter="letter">
                            </letter>
                        </div>
                    </letter-list>

                    <move-list game-id="{{ $playerGame->game_id }}" inline-template>
                        <div>
                            <span>Moves: @{{ moves.length }}</span>
                            <table>
                                <tr v-for="move in moves">
                                    <td>@{{ move.guess }}</td>
                                    <td>@{{ move.result }} </td>
                                </tr>
                            </table>
                        </div>
                    </move-list>
                </div>

                <guess-box game-turn="{{ $playerGame->turn }}" my-id="{{ $playerGame->player_id }}" game-id="{{ $playerGame->game_id }}" inline-template>
                    <div v-if="this.myturn" >
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
