@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <start-game inline-template>
                        <div>
                            <h2>Start A New Game</h2>
                            <generic-form posturl="/api/game/create" success="onGameCreate">
                                <select-input :items="friends" name="opponent_id" label="Opponent"></select-input>
                                <text-input name="max_length" label="Max letters (6-12)" prompt="Max length of word"></text-input>
                                <text-input name="max_recurrance" label="Max duplicates (1-4)" prompt="# of dup. letters allowed"></text-input>
                                <choose-word></choose-word>
                                <button class="button is-primary">Submit</button>
                            </generic-form>
                        </div>
                    </start-game>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('components')
    <script src="{{ asset('js/create-game.js') }}" defer></script>
@endsection