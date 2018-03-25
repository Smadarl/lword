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

                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

<!--                    You are logged in! -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
