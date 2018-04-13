@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <game game-id="{{ $playerGame->game_id }}" inline-template>
                <div class="card">
                    <a href="{{ route('games') }}">Games List</a>
                    <h2>Game with {{ $playerGame->opponent_name }}</h2>
                    <div class="card-header">
                        <h3>Letters</h3>

                        <letter-list inline-template>
                            <div>
                                <ul class="letter-list">

                                    <letter v-for="(count, letter) in this.$store.state.letters" :the-letter="letter" :key="letter">
                                    </letter>

                                </ul>
                                <button @click="save">Save</button>
                            </div>
                        </letter-list>

                        <div style="clear: both"></div>

                        <move-list>
                        </move-list>

                    </div>

                    <guess-box inline-template>
                        <div v-if="this.myturn" >
                            <h3>Your Turn</h3>
                            <form method="POST" action="/game/move" @submit.prevent="onSubmit">
                                <input type="text" id="guess" name="guess" class="input" v-model="guess"/>
                                <button class="button is-primary">Submit</button>
                            </form>
                        </div>
                        <div v-else>
                            <button class="button is-primary" @click="checkTurn">Check turn</button>
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
            </game>
        </div>
    </div>
</div>
@endsection
