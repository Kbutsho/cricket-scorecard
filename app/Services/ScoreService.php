<?php

namespace App\Services;

use App\Models\Score;

class ScoreService
{
    public function inningsTotalScore($matchId, $teamId)
    {
        $totalScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $teamId)
            ->get();
        $totalWicket = $totalScore->sum('wicket');
        $totalBall = $totalScore->sum('ball');
        $totalOver = floor($totalBall / 6) . '.' . ($totalBall % 6);
        $totalFours = $totalScore->where('run', 4)->count();
        $totalSixes = $totalScore->where('run', 6)->count();
        $totalExtraRuns = 0;
        foreach ($totalScore as $total) {
            $extraRun = 0;
            if (preg_match('/^(NB|WD|LB|B)(\d+)?$/i', $total->extra, $matches)) {
                $extraRun = isset($matches[2]) ? (int)$matches[2] : 1;
            }
            $totalExtraRuns += $extraRun;
        }
        $totalRuns = $totalScore->sum('run') + $totalExtraRuns;
        if ($totalBall > 0) {
            $runRate = number_format($totalRuns /  ($totalBall / 6), 2);
        } else {
            $runRate = 0;
        }
        return [
            'totalScore' => $totalScore,
            'totalWickets' => $totalWicket,
            'totalBalls' => $totalBall,
            'totalOvers' => $totalOver,
            'totalFours' => $totalFours,
            'totalSixes' => $totalSixes,
            'totalExtraRuns' => $totalExtraRuns,
            'totalRuns' => $totalRuns,
            'runRate' => $runRate
        ];
    }
}
