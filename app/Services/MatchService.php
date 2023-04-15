<?php

namespace App\Services;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Score;
use App\Models\Squad;
use Exception;

class MatchService
{
    public function getMatchData($matchId)
    {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            ->where('id', '=', $matchId)
            ->where('time', '<', $currentDate)
            ->first();
        $matchInInnings = Innings::where('match_id', $matchId)->first();
        if (!$match || !$matchInInnings) {
            throw new Exception('Match ' . $matchId . ' not found!');
        }
        $firstInnings = Innings::where('match_id', $matchId)
            ->where('innings', 1)
            ->first(['battingTeam_id', 'bowlingTeam_id']);

        $firstBattingSquad = Squad::where('team_id', $firstInnings->battingTeam_id)->where('match_id', $matchId)->get();
        $firstBowlingSquad = Squad::where('team_id', $firstInnings->bowlingTeam_id)->where('match_id', $matchId)->get();

        return [
            'match' => $match,
            'firstBattingSquad' => $firstBattingSquad,
            'firstBowlingSquad' => $firstBowlingSquad
        ];
    }
    public function matchOtherData($matchId, $firstTeamId, $secondTeamId)
    {
        $runningInningsStatus = Innings::where('match_id', $matchId)->get();
        $inningsOne = 0;
        $inningsTwo = 0;
        foreach ($runningInningsStatus as $innings) {
            if ($innings->innings == 1) {
                $inningsOne = $innings->status;
            } elseif ($innings->innings == 2) {
                $inningsTwo = $innings->status;
            }
        }
        $inningsStatus = [
            'inningsOne' => $inningsOne,
            'inningsTwo' => $inningsTwo
        ];
        $outBatsmanList = Score::where('match_id', $matchId)->where('wicket', 1)->pluck('batsman_id')->toArray();
        $firstTeamScoreLine = Score::where('battingTeam_id', $firstTeamId)
            ->where('match_id', $matchId)->get(['score_line']);
        $secondTeamScoreLine = Score::where('battingTeam_id', $secondTeamId)
            ->where('match_id', $matchId)->get(['score_line']);
        return [
            'inningsStatus' => $inningsStatus,
            'outBatsmanList' => $outBatsmanList,
            'firstTeamScoreLine' => $firstTeamScoreLine,
            'secondTeamScoreLine' => $secondTeamScoreLine
        ];
    }
}
