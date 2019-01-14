@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <generic-form posturl="/test" success="onSuccess" fail="onFail">
                <text-input name="opponent_id" prompt="Opponent" label="Opponent ID"></text-input>
                <br/>
                <text-input name="max_length" prompt="Max Size" label="Max word length"></text-input>
                <br/>
                <text-input name="max_recurrance" prompt="Max Recur" label="Max duplicate letters"></text-input>
                <choose-word></choose-word>
                <button>Submit</button>
                <!-- <text-input name="name" prompt="Name" label="What is your name"></text-input>
                <br/>
                <text-input name="quest" prompt="Your Quest" label="What is your quest"></text-input> -->
            </generic-form>
        </div>
    </div>
</div>
@endsection

@section('components')
    <script src="{{ asset('js/test.js') }}" defer></script>
@endsection