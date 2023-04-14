<?php

namespace App\Services;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Squad;

class MatchService
{
    public function getMatch($matchId)
    {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            ->where('id', '=', $matchId)
            ->where('time', '<', $currentDate)
            ->first();
        $matchInInnings = Innings::where('match_id', $matchId)->first();
        if (!$match || !$matchInInnings) {
            return null;
        }
        $TeamData = Innings::where('match_id', $matchId)
            ->whereIn('innings', [1, 2])
            ->select('battingTeam_id', 'bowlingTeam_id')
            ->get();
        $battingTeams = $TeamData->pluck('battingTeam_id')->toArray();
        $bowlingTeams = $TeamData->pluck('bowlingTeam_id')->toArray();

        $firstBattingSquad = Squad::where('team_id', $battingTeams[0])->where('match_id', $matchId)->get();
        $firstBowlingSquad = Squad::where('team_id', $bowlingTeams[0])->where('match_id', $matchId)->get();
        $secondBattingSquad = $firstBowlingSquad;
        $secondBowlingSquad = $firstBattingSquad;
    

        return [
            'battingTeams' => $battingTeams,
            'bowlingTeams' => $bowlingTeams,
            'firstBowlingSquad' =>$firstBowlingSquad,
            'secondBattingSquad' => $secondBattingSquad,
            'secondBowlingSquad' => $secondBowlingSquad
        ];
    }
}
