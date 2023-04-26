<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveMatchController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'liveMatchList'])->name('home');
Route::get('live/match/{id}', [HomeController::class, 'liveMatch'])->name('live');

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'ShowLoginForm'])->name('get.login');
    Route::post('login', [AuthController::class, 'Login'])->name('login');
   
});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [AuthController::class, 'Dashboard'])->name('dashboard');
    Route::get('logout', [AuthController::class, 'Logout'])->name('logout');

    // venue
    Route::get('venues', [VenueController::class, 'ShowVenueList'])->name('venues');
    Route::get('add-venue', [VenueController::class, 'ShowAddVenueForm'])->name('get.add-venue');
    Route::post('add-venue', [VenueController::class, 'AddVenue'])->name('add-venue');
    Route::get('venue/{id}/update', [VenueController::class, 'UpdateVenueForm'])->name('get.venue-update');
    Route::patch('venue/update', [VenueController::class, 'UpdateVenue'])->name('venue.update');
    Route::delete('venue/{id}/delete', [VenueController::class, 'DeleteVenue'])->name('venue.delete');

    // team
    Route::get('teams', [TeamController::class, 'ShowTeamList'])->name('teams');
    Route::get('add-team', [TeamController::class, 'ShowAddTeamForm'])->name('get.add-team');
    Route::post('add-team', [TeamController::class, 'AddTeam'])->name('add-team');
    Route::get('team/{id}/update', [TeamController::class, 'UpdateTeamForm'])->name('get.team-update');
    Route::patch('team/update', [TeamController::class, 'UpdateTeam'])->name('team.update');
    Route::delete('team/{id}/delete', [TeamController::class, 'DeleteTeam'])->name('team.delete');
    Route::get('team/{id}/players', [TeamController::class, 'getTeamAllPlayersForm'])->name('get.team-players');

    //player
    Route::get('players', [PlayerController::class, 'ShowPlayerList'])->name('players');
    Route::get('add-player', [PlayerController::class, 'ShowAddPlayerForm'])->name('get.add-player');
    Route::post('add-player', [PlayerController::class, 'AddPlayer'])->name('add-player');
    Route::get('player/{id}/update', [PlayerController::class, 'UpdatePlayerForm'])->name('get.player-update');
    Route::patch('player/update', [PlayerController::class, 'UpdatePlayer'])->name('player.update');
    Route::delete('player/{id}/delete', [PlayerController::class, 'DeletePlayer'])->name('player.delete');

    //match
    Route::get('matches', [MatchController::class, 'ShowMatchList'])->name('matches');
    Route::get('add-match', [MatchController::class, 'ShowAddMatchForm'])->name('get.add-match');
    Route::post('add-match', [MatchController::class, 'AddMatch'])->name('add-match');


    // working
    Route::get('live/match', [LiveMatchController::class, 'ShowAdminLiveMatchList'])->name('get.live.matches');
    Route::get('live/match/{id}/squad', [LiveMatchController::class, 'showSquadForm'])->name('get.live.match.squad');
    Route::post('live/match/{id}/squad', [LiveMatchController::class, 'saveSquad'])->name('post.live.match.squad');
    //score working
    Route::get('live/match/{id}/score', [ScoreController::class, 'showAdminScore'])->name('get.live.match.score');
    Route::post('live/match/{id}/score', [ScoreController::class, 'updateScore'])->name('post.live.match.score');
    Route::get('live/match/{matchId}/update/innings', [ScoreController::class, 'updateInnings'])->name('update.live.match.innings');
    // select squad

    // Route::post('match/live/{id}/squad/save', [LiveMatchController::class, 'saveSquad'])->name('save.live-match.squad');
    // Route::get('/match/{id}/live/score', function () {
    //     return view('pages.matches.live.liveMatchUpdateDashboard');
    // });

    // score controller

    // Route::post('match/live/{id}/update/score', [ScoreController::class, 'updateScore'])->name('match.update.score');
    // // innings from 1 to 2
    // Route::post('/update-innings', 'ScoreController@updateInnings')->name('update-innings');
});
