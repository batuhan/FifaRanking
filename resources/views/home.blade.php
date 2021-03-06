@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Home</div>
                    <div class="panel-body">

                        <form class="form-horizontal" method="POST" action="{{ route('games.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('player_one_id') ? ' has-error' : '' }}">
                                <div class="col-md-6">
                                    <select id="player_one_id" class="form-control" name="player_one_id"
                                            value="{{ old('player_one_id') }}">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('player_one_id'))
                                        <span class="help-block text-muted">
                                        {{ $errors->first('player_one_id') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-md-6"{{ $errors->has('player_one_id') ? ' has-error' : '' }}>
                                    <select id="player_two_id" class="form-control" name="player_two_id"
                                            value="{{ old('player_two_id') }}">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('player_two_id'))
                                        <span class="help-block text-muted">
                                        {{ $errors->first('player_two_id') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('player_one_score') ? ' has-error' : '' }}">
                                <div class="col-md-6">
                                    <input id="player_one_score" type="number" placeholder="Player One Score"
                                           class="form-control" name="player_one_score"
                                           value="{{ old('player_one_score') }}" required autofocus>

                                    @if ($errors->has('player_one_score'))
                                        <span class="help-block text-muted">
                                        {{ $errors->first('player_one_score') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-md-6"{{ $errors->has('player_two_score') ? ' has-error' : '' }}>
                                    <input id="player_two_score" type="number" placeholder="Player Two Score"
                                           class="form-control" name="player_two_score"
                                           value="{{ old('player_two_score') }}" required autofocus>

                                    @if ($errors->has('player_two_score'))
                                        <span class="help-block text-muted">
                                        {{ $errors->first('player_two_score') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-block">
                                        Register Game
                                    </button>
                                </div>
                            </div>

                        </form>

                        <hr>


                        <table class="table table-striped text-muted">
                            <thead>
                            <tr>
                                <th>Rating</th>
                                <th>Change</th>
                                <th>Name</th>
                                <th>Played</th>
                                <th>Won</th>
                                <th>Lost</th>
                                <th>Draw</th>
                                <th>Scored</th>
                                <th>Against</th>
                                <th>Diff</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($ratings as $rating)
                                <tr>
                                    <td>{{ $rating->rating }}</td>
                                    <td>{{ $rating->rating_change }}</td>
                                    <td>{{ $rating->user->name }}</td>
                                    <td>{{ $rating->user->statistics->played_games }}</td>
                                    <td>{{ $rating->user->statistics->games_won }}</td>
                                    <td>{{ $rating->user->statistics->games_lost }}</td>
                                    <td>{{ $rating->user->statistics->games_drawn }}</td>
                                    <td>{{ $rating->user->statistics->goals_scored }}</td>
                                    <td>{{ $rating->user->statistics->goals_against }}</td>
                                    <td>{{ $rating->user->statistics->goal_difference }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <hr>

                        <table class="table table-striped text-muted">
                            <thead>
                            <tr>
                                <th>P1</th>
                                <th>P1 Score</th>
                                <th>P2</th>
                                <th>P2 Score</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($games as $game)
                                <tr>
                                    <td>{{ $game->userA->name }}</td>
                                    <td>{{ $game->user_a_score }}</td>
                                    <td>{{ $game->userB->name }}</td>
                                    <td>{{ $game->user_b_score }}</td>
                                    <td>{{ $game->created_at }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
