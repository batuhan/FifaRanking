<?php

namespace App\Http\Controllers;

use App\Game;
use App\User;
use Illuminate\Http\Request;
use Validator;


class GamesController extends Controller
{

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'player_one_id' => 'required',
            'player_two_id' => 'required',
            'player_one_score' => 'required',
            'player_two_score' => 'required',
        ]);

        /**
         * Get users/players
         */
        $player_one = User::find($request['player_one_id']);
        $player_two = User::find($request['player_two_id']);

        /**
         * Calculate Result
         * 1 = WIN
         * 0 = LOOSE
         * 0.5 = DRAW
         */
        $score_list = $this->calculateResult($request["player_one_score"], $request["player_two_score"]);
        $player_one_score = $score_list["player_one_score"];
        $player_two_score = $score_list["player_two_score"];


        /**
         * Get player ratings
         */
        $player_one_rating = $player_one->rating;
        $player_two_rating = $player_two->rating;

        /**
         * Calculate new ratings
         */
        $new_ratings = $this->calculateNewRating($player_one_rating->rating, $player_two_rating->rating, $player_one_score, $player_two_score);

        /**
         * Set new ratings
         */
        $this->setNewRatings($new_ratings, $player_one_rating, $player_two_rating);

        /**
         * Create the game
         */
        $game = Game::create([
            'user_a_id' => $request['player_one_id'],
            'user_b_id' => $request['player_two_id'],
            'user_a_score' => $request['player_one_score'],
            'user_b_score' => $request['player_two_score'],
        ]);

        /**
         * Update statistics
         */
        $this->updateStatistics($player_one->statistics, $game->user_a_score, $game->user_b_score, $player_one_score);
        $this->updateStatistics($player_two->statistics, $game->user_b_score, $game->user_a_score, $player_two_score);


        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Game $game
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Game $game
     * @return \Illuminate\Http\Response
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Game $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Game $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }

    protected function calculateNewRating($player_one_rating, $player_two_rating, $player_one_score, $player_two_score)
    {
        $curl = curl_init();
        $url = 'http://eloratingapi.azurewebsites.net/api/Ratings?'
            . "ratingA=" . $player_one_rating
            . "&ratingB=" . $player_two_rating
            . "&scoreA=" . $player_one_score
            . "&scoreB=" . $player_two_score;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

        return $response;
    }

    protected function setNewRatings($new_ratings, $player_one_rating, $player_two_rating)
    {
        $new_ratings = json_decode($new_ratings, true);

        $player_one_rating->rating = $new_ratings["RatingA"];
        $player_two_rating->rating = $new_ratings["RatingB"];
        $player_one_rating->rating_change = $new_ratings["RatingChangeA"];
        $player_two_rating->rating_change = $new_ratings["RatingChangeB"];

        $player_one_rating->save();
        $player_two_rating->save();
    }

    protected function calculateResult($player_one_score, $player_two_score)
    {

        if ($player_one_score > $player_two_score) {
            $result_one = 1;
            $result_two = 0;

        } else if ($player_two_score > $player_one_score) {
            $result_one = 0;
            $result_two = 1;

        } else if ($player_one_score === $player_one_score) {
            $result_one = 0.5;
            $result_two = 0.5;
        }

        return [
            'player_one_score' => $result_one,
            'player_two_score' => $result_two
        ];
    }

    protected function updateStatistics($player_statistics, $goals_scored, $goals_against, $game_score)
    {
        $player_statistics->played_games++;
        $player_statistics->goals_scored += $goals_scored;
        $player_statistics->goals_against += $goals_against;
        $player_statistics->goal_difference = $player_statistics->goals_scored - $player_statistics->goals_against;

        if ($game_score === 1) {
            $player_statistics->games_won++;

        } else if ($game_score === 0) {
            $player_statistics->games_lost++;

        } else if ($game_score === 0.5) {
            $player_statistics->games_drawn++;
        }

        $player_statistics->save();
    }

}
