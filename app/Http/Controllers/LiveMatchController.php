<?php

namespace App\Http\Controllers;

use App\Models\BattingSquad;
use App\Models\BowlingSquad;
use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Player;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Team;
use Illuminate\Http\Request;

class LiveMatchController extends Controller
{
    // working
    public function ShowAdminLiveMatchList()
    {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        CricketMatch::where('time', '<', $currentDate)
            ->update(['status' => 'ongoing']);
        $matches = CricketMatch::with(['teamA', 'teamB'])
            ->where('time', '<', $currentDate)
            ->get();
        // Get innings in progress for each match
        // $innings = Innings::where('status', 1)->get();
        // $matchInningsInProgress = [];
        // foreach ($innings as $inningsItem) {
        //     $matchId = $inningsItem->match_id;
        //     $inningsNumber = $inningsItem->innings;
        //     if ($matches->where('id', $matchId)->first()) {
        //         $matchInningsInProgress[$matchId] = "Innings $inningsNumber running";
        //     } else {
        //         $matchInningsInProgress[$matchId] = "match not started yet!";
        //     }
        // }
        return view('pages.matches.live.liveMatchList', [
            // 'matchInningsInProgress' => $matchInningsInProgress,
            'liveMatches' => $matches,
        ]);
    }
    // working
    public function showSquadForm($id)
    {
        // $innings = Innings::where('match_id', '=', $id)->first();
        // if ($innings) {
        //     if ($innings->innings == '1' && $innings->status == '1') {
        //         $battingTeam = Team::find($innings->battingTeam_id);
        //         $bowlingTeam = Team::find($innings->bowlingTeam_id);
        //         $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        //         $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
        //             ->where('id', '=', $id)
        //             ->where('time', '<', $currentDate)
        //             ->first();

        //         // here also have to check status for 2nd innings
        //         $battingSquad = Squad::where('role', 'bat')
        //             ->where('match_id', $id)
        //             ->get(['player_id', 'player_name', 'team_id', 'team_name']);

        //         $bowlingSquad = Squad::where('role', 'ball')
        //             ->where('match_id', $id)
        //             ->get(['player_id', 'player_name', 'team_id', 'team_name']);

        //         $newScore = Score::where('match_id', $id)->get();
        //         $totalRuns = $newScore->sum('run');
        //         $totalWicket = $newScore->sum('wicket');
        //         $totalOver = intval($newScore->sum('ball') / 6);
        //         $overCarry = $newScore->sum('ball') % 6;
        //         $individualRuns = $newScore->groupBy('batsman_id')
        //             ->map(function ($batsmanScores) {
        //                 return $batsmanScores->sum('run');
        //             });
        //         $individualRuns = $individualRuns->mapWithKeys(function ($runTotal, $batsmanId) {
        //             $playerName = Player::where('id', $batsmanId)->value('name');
        //             return [$playerName => $runTotal];
        //         });
        //         return view(
        //             'pages.matches.live.liveMatchUpdateDashboard',
        //             [
        //                 'individualRuns' => $individualRuns,
        //                 'totalRuns' => $totalRuns,
        //                 'totalWicket' => $totalWicket,
        //                 'totalOver' => $totalOver,
        //                 'overCarry' => $overCarry,
        //                 'newScore' => $newScore,
        //                 'battingSquad' => $battingSquad,
        //                 'bowlingSquad' => $bowlingSquad,
        //                 'battingTeam' => $battingTeam,
        //                 'bowlingTeam' => $bowlingTeam,
        //                 'match' => $match
        //             ]
        //         );
        //     }
        // } else {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            ->where('id', '=', $id)
            ->where('time', '<', $currentDate)
            ->first();
        $checkInnings = Innings::where('match_id', $id)->get();
        if ($checkInnings && count($checkInnings) > 0) {
            return redirect()->route('get.live.match.score', ['id' => $id]);
            // ->withSuccess('squad already selected!');
        }
        if (!$match) {
            return redirect('dashboard')->withDanger('match id ' . $id . 'not found');
        }
        return view(
            'pages.matches.live.liveMatchSquadForm',
            [
                'match' => $match
            ]
        );
    }
    // public function ShowLiveMatchUpdateDashboard($id)
    // {
    //     $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
    //     $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
    //         ->where('id', '=', $id)
    //         ->where('time', '<', $currentDate)
    //         ->first();
    //     if (!$match) {
    //         return redirect('match/live')->withDanger('live match id ' . $id . ' not found');
    //     }
    //     return view('pages.matches.live.liveMatchUpdateDashboard', ['match' => $match]);
    // }
    //working
    public function saveSquad(Request $request, $id)
    {
        // dd($request->all());
        $request->validate(
            [
                'firstBattingTeamId' => 'required',
                'firstBowlingTeamId' => 'required',
                'teamA' => 'required|array|size:11',
                'teamB' => 'required|array|size:11',
            ],
            [
                'firstBattingTeamId.required' => 'select batting team',
                'firstBowlingTeamId.required' => 'select bowling team',
                'teamA.required' => 'select 11 players',
                'teamB.required' => 'select 11 players',
                'teamA.size' => 'select 11 players',
                'teamB.size' => 'select 11 players',
            ]
        );
        // save innings 1 
        $firstInnings = 1;
        $firstBattingTeamId = $request->firstBattingTeamId;
        $firstBowlingTeamId = $request->firstBowlingTeamId;
        $innings1 = new Innings();
        $innings1->match_id = $request->match_id;
        $innings1->battingTeam_id = $firstBattingTeamId;
        $innings1->bowlingTeam_id = $firstBowlingTeamId;
        $innings1->innings = $firstInnings;
        $innings1->status = 1;
        $innings1->save();

        // save innings 2 
        $secondInnings = 2;
        $secondBattingTeamId = $request->firstBowlingTeamId;
        $secondBowlingTeamId = $request->firstBattingTeamId;
        $innings2 = new Innings();
        $innings2->match_id = $request->match_id;
        $innings2->battingTeam_id = $secondBattingTeamId;
        $innings2->bowlingTeam_id = $secondBowlingTeamId;
        $innings2->innings = $secondInnings;
        $innings2->status = 0;
        $innings2->save();

        $player_id = $request->input('player_id');
        $player_name = $request->input('player_name');
        $team_id = $request->input('team_id');
        $team_name = $request->input('team_name');

        foreach ($player_id as $i => $data) {
            $var = new Squad();
            $var->match_id = $request->match_id;
            $var->player_id = $player_id[$i];
            $var->player_name = $player_name[$i];
            $var->team_id = $team_id[$i];
            $var->team_name = $team_name[$i];
            $var->save();
        }
        return redirect()->route('get.live.match.score', ['id' => $id])
            ->withSuccess('squad selected successfully!');

        // $match = CricketMatch::where('id', '=', $id)->first();
        // $teamA = Team::where('id', '=', $match->team_a_id);
        // $teamB = Team::where('id', '=', $match->team_b_id);

        // return redirect('get.live.match.score', ['id' => $id]);
        // return view('pages.matches.live.liveMatchSquad', ['innings' => $innings, 'match_id' => $id, 'battingTeam' => $battingTeam, 'bowlingTeam' => $bowlingTeam]);
    }

    // public function saveSquad(Request $request, $id)
    // {
    //     //dd($request->all());
    //     $check = CricketMatch::find($id);
    //     if (!$check) {
    //         return redirect()->back()->withDanger('match id' . $id . 'not found');
    //     } else {
    //         $battingTeam = Team::find($request->input('battingTeamId'));
    //         $bowlingTeam = Team::find($request->input('bowlingTeamId'));
    //         $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
    //         $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
    //             ->where('id', '=', $id)
    //             ->where('time', '<', $currentDate)
    //             ->first();
    //         $newScore = Score::where('match_id', $id)->get();
    //         $totalRuns = $newScore->sum('run');
    //         $totalWicket = $newScore->sum('wicket');
    //         $totalOver = intval($newScore->sum('ball') / 6);
    //         $overCarry = $newScore->sum('ball') % 6;
    //         $individualRuns = $newScore->groupBy('batsman_id')
    //             ->map(function ($batsmanScores) {
    //                 return $batsmanScores->sum('run');
    //             });

    //         $individualRuns = $individualRuns->mapWithKeys(function ($runTotal, $batsmanId) {
    //             $playerName = Player::where('id', $batsmanId)->value('name');
    //             return [$playerName => $runTotal];
    //         });


    //         foreach ($request->input('player_id') as $key => $playerId) {

    //             $squad = new Squad();
    //             $squad->match_id = $id;
    //             $squad->player_id = $playerId;
    //             $squad->player_name = $request->input('player_name')[$key];
    //             $squad->team_id = $request->input('team_id')[$key];
    //             $squad->team_name = $request->input('team_name')[$key];
    //             $squad->role = $request->input('role')[$key];
    //             $squad->innings = $request->input('innings');
    //             $squad->save();
    //         }

    //         // foreach ($request->input('player_id') as $key => $playerId) {
    //         //     $squad1 = new Squad();
    //         //     $squad1->match_id = $id;
    //         //     $squad1->player_id = $playerId;
    //         //     $squad1->player_name = $request->input('player_name')[$key];
    //         //     $squad1->team_id = $request->input('team_id')[$key];
    //         //     $squad1->team_name = $request->input('team_name')[$key];
    //         //     $squad1->role = 'bat';
    //         //     $squad1->innings = '1st';
    //         //     $squad1->save();

    //         //     $squad2 = new Squad();
    //         //     $squad2->match_id = $id;
    //         //     $squad2->player_id = $playerId;
    //         //     $squad2->player_name = $request->input('player_name')[$key];
    //         //     $squad2->team_id = $request->input('team_id')[$key];
    //         //     $squad2->team_name = $request->input('team_name')[$key];
    //         //     $squad2->role = ($request->input('role')[$key] == 'bat' && $request->input('innings') == '1st') ? 'ball' : $request->input('role')[$key];
    //         //     $squad2->innings = ($request->input('role')[$key] == 'bat' && $request->input('innings') == '1st') ? '2nd' : $request->input('innings');
    //         //     $squad2->save();
    //         // }

    //         // save info to innings table 
    //         $innings = new Innings();
    //         $innings->match_id = $id;
    //         $innings->battingTeam_id = $request->battingTeamId;
    //         $innings->bowlingTeam_id = $request->bowlingTeamId;
    //         $innings->innings = 1;
    //         $innings->status = 1;
    //         $innings->save();

    //         $squadPlayers = Squad::where('match_id', $id)->get();
    //         $battingSquad = array();
    //         $bowlingSquad = array();
    //         foreach ($squadPlayers as $player) {
    //             if ($player->role == 'bat') {
    //                 $battingSquad[] = array(
    //                     'id' => $player->team_id,
    //                     'name' => $player->team_name,
    //                     'player_id' => $player->player_id,
    //                     'player_name' => $player->player_name
    //                 );
    //             } else if ($player->role == 'ball') {
    //                 $bowlingSquad[] = array(
    //                     'id' => $player->team_id,
    //                     'name' => $player->team_name,
    //                     'player_id' => $player->player_id,
    //                     'player_name' => $player->player_name
    //                 );
    //             }
    //         }
    //         return view(
    //             'pages.matches.live.liveMatchUpdateDashboard',
    //             [
    //                 'individualRuns' => $individualRuns,
    //                 'totalRuns' => $totalRuns,
    //                 'totalWicket' => $totalWicket,
    //                 'totalOver' => $totalOver,
    //                 'overCarry' => $overCarry,
    //                 'newScore' => $newScore,
    //                 'battingSquad' => $battingSquad,
    //                 'bowlingSquad' => $bowlingSquad,
    //                 'battingTeam' => $battingTeam,
    //                 'bowlingTeam' => $bowlingTeam,
    //                 'match' => $match
    //             ]
    //         );
    //     }
    // }

    // public function ShowLiveMatchUpdateDashboard(Request $request, $id)
    // {
    //     $request->validate(
    //         [
    //             'batting_team' => 'required',
    //             'bowling_team' => 'required',
    //         ],
    //         [
    //             'batting_team.required' => 'select batting team',
    //             'bowling_team.required' => 'select bowling team',
    //         ]
    //     );
    //     $battingTeam = Team::find($request->input('batting_team'));
    //     $bowlingTeam = Team::find($request->input('bowling_team'));

    //     $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
    //     $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
    //         ->where('id', '=', $id)
    //         ->where('time', '<', $currentDate)
    //         ->first();

    //     $newScore = Score::where('match_id', $id)->get();
    //     $totalRuns = $newScore->sum('run');
    //     $totalWicket = $newScore->sum('wicket');
    //     $totalOver = intval($newScore->sum('ball') / 6);
    //     $overCarry = $newScore->sum('ball') % 6;
    //     $individualRuns = $newScore->groupBy('batsman_id')
    //         ->map(function ($batsmanScores) {
    //             return $batsmanScores->sum('run');
    //         });

    //     $individualRuns = $individualRuns->mapWithKeys(function ($runTotal, $batsmanId) {
    //         $playerName = Player::where('id', $batsmanId)->value('name');
    //         return [$playerName => $runTotal];
    //     });
    //     return view(
    //         'pages.matches.live.liveMatchUpdateDashboard',
    //         [
    //             'individualRuns' => $individualRuns,
    //             'totalRuns' => $totalRuns,
    //             'totalWicket' => $totalWicket,
    //             'totalOver' => $totalOver,
    //             'overCarry' => $overCarry,
    //             'newScore' => $newScore,
    //             'battingTeam' => $battingTeam,
    //             'bowlingTeam' => $bowlingTeam,
    //             'match' => $match
    //         ]
    //     );
    // }
}
