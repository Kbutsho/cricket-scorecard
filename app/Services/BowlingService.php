<?php

namespace App\Services;

use App\Models\Score;
use App\Models\Squad;

class BowlingService
{
   
    public function individualScore($matchId, $bowlingTeamId)
    {
        $bowlingTeamScore = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $bowlingTeamId)
            ->get();

        $bowlingIndividualScore = $bowlingTeamScore->groupBy('bowler_id')->map(function ($bowlerScores) {
            $bowlerName = Squad::where('player_id', $bowlerScores->first()->bowler_id)->value('player_name');
            
            $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                $extraRun = preg_match('/^(NB|WD|LB|B)(\d+)?$/i', $ball->extra, $matches) ? (int)($matches[2] ?? 1) : 0;
                $carry['extra_runs'] += $extraRun;
                $carry['no_ball_runs'] += (int)(strpos($ball->extra, 'NB') !== false) * $extraRun;
                $carry['wide_runs'] += (int)(strpos($ball->extra, 'WD') !== false) * $extraRun;
                $carry['no_ball_count'] += (int)(strpos($ball->extra, 'NB') !== false);
                $carry['wide_ball_count'] += (int)(strpos($ball->extra, 'WD') !== false);
                return $carry;
            }, ['extra_runs' => 0, 'no_ball_runs' => 0, 'wide_runs' => 0, 'no_ball_count' => 0, 'wide_ball_count' => 0]);

            $totalRuns = $bowlerScores->sum('run') + $extraData['extra_runs'];
            $totalBalls = $bowlerScores->sum('ball');
            $totalFours = $bowlerScores->where('run', 4)->count();
            $totalSixes = $bowlerScores->where('run', 6)->count();
            $totalWicket = $bowlerScores->where('wicket', 1)->count();
            $totalOvers = floor($totalBalls / 6) . '.' . ($totalBalls % 6);
            $economyRate = $totalBalls != 0 ? round(($totalRuns) / ($totalBalls / 6), 2) : 0;

            return [
                'bowlerName' => $bowlerName,
                'bowler_id' => $bowlerScores->first()->bowler_id,
                'runs' => $totalRuns,
                'balls' => $totalBalls,
                'fours' => $totalFours,
                'sixes' => $totalSixes,
                'wickets' => $totalWicket,
                'totalExtra' => $extraData['extra_runs'],
                'totalNoBallRuns' => $extraData['no_ball_runs'],
                'totalWideRuns' => $extraData['wide_runs'],
                'totalNoCount' => $extraData['no_ball_count'],
                'totalWideCount' => $extraData['wide_ball_count'],
                'economyRate' => $economyRate,
                'totalOvers' => $totalOvers
            ];
        });
        return $bowlingIndividualScore;
    }
    public function mostEconomicalBowler($bowlingIndividualScore)
    {
        $mostEconomicalBowler = collect($bowlingIndividualScore)->sortBy('economyRate')->first() ?? [];
        return [
            'bowlerName' => $mostEconomicalBowler['bowlerName'] ?? '',
            'bowler_id' => $mostEconomicalBowler['bowler_id'] ?? 0,
            'runs' => $mostEconomicalBowler['runs'] ?? 0,
            'balls' => $mostEconomicalBowler['balls'] ?? 0,
            'fours' => $mostEconomicalBowler['fours'] ?? 0,
            'sixes' => $mostEconomicalBowler['sixes'] ?? 0,
            'wickets' => $mostEconomicalBowler['wickets'] ?? 0,
            'totalExtra' => $mostEconomicalBowler['totalExtra'] ?? 0,
            'totalNoBallRuns' => $mostEconomicalBowler['totalNoBallRuns'] ?? 0,
            'totalWideRuns' => $mostEconomicalBowler['totalWideRuns'] ?? 0,
            'totalNoCount' => $mostEconomicalBowler['totalNoCount'] ?? 0,
            'totalWideCount' => $mostEconomicalBowler['totalWideCount'] ?? 0,
            'economyRate' => $mostEconomicalBowler['economyRate'] ?? 0,
            'totalOvers' => $mostEconomicalBowler['totalOvers'] ?? 0
        ];
    }
}
