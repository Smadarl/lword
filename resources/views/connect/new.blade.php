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
                            <h2>Start A New Connect Game</h2>
                            <generic-form posturl="/api/connect/create" success="onGameCreate">
                                <select-input :items="friends" name="opponent_id" label="Opponent"></select-input>
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
    <script src="{{ asset('js/connect/new.js') }}" defer></script>
@endsection