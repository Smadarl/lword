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

                        <start-game inline-template>
                            <div>
                                <form method="POST" action="/game/create" @submit.prevent="onSubmit">
                                    <label for="friend">Friend</label>
                                    <select id="friend" v-model="opponentid">
                                        <option v-for="friend in friends" v-bind:value="friend.friend_id" v-text="friend.friend_name" />
                                    </select>
                                    <br/>

                                    <label for="maxrecur">Max Recur</label>
                                    <input type="text" id="maxrecur" v-bind:value="maxrecur" />
                                    <br/>

                                    <label for="maxlength">Max Length</label>
                                    <input type="text" id="maxlength" v-bind:value="maxlength" />
                                    <br/>

                                    <input type="radio" id="choose" value="choose" v-model="origination">
                                    <label for="choose">Choose my own word</label><br/>
                                    <input type="radio" id="random" value="random" v-model="origination">
                                    <label for="random">Random word</label><br/>
                                    <div v-if="origination == 'choose'">
                                        <label for="chosenWord">My Word</label>
                                        <input type="text" id="chosenWord" v-model="myword" />
                                    </div>

                                    <button class="button is-primary">Submit</button>
                                </form>
                            </div>
                        </start-game>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
