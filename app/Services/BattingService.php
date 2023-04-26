<?php

namespace App\Services;

use App\Models\Squad;
use Illuminate\Support\Collection;

class BattingService
{
    public function individualScore(Collection $battingScore)
    {
        return $battingScore->groupBy('batsman_id')
            ->map(function ($batsmanScores ) {
                $totalRuns = $batsmanScores->sum('run');
                $totalBalls = $batsmanScores->sum('ball');
                $totalFours = $batsmanScores->where('run', 4)->count();
                $totalSixes = $batsmanScores->where('run', 6)->count();
                $strikeRate = ($totalBalls > 0) ? round(($totalRuns / $totalBalls) * 100, 2) : 0;
                $batsmanName = Squad::where('player_id', $batsmanScores->first()->batsman_id)->value('player_name');
                $outByBowlerName = '';
                if ($batsmanScores->where('wicket', 1)->isNotEmpty()) {
                    $bowlerId = $batsmanScores->where('wicket', 1)->first()['bowler_id'];
                    $outByBowlerName = Squad::where('player_id', $bowlerId)->value('player_name');
                }
                return [
                    'batsmanName' => $batsmanName,
                    'outByBowlerName' => $outByBowlerName,
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                    'strike_rate' => $strikeRate,
                ];
            });
    }
    public function highestRunScorer($teamIndividualScore)
    {
        $highestRunScorer = collect($teamIndividualScore)->sortByDesc('runs')->first() ?? [];
        return [
            'batsmanName' => $highestRunScorer['batsmanName'] ?? '',
            'outByBowlerName' => $highestRunScorer['outByBowlerName'] ?? '',
            'runs' => $highestRunScorer['runs'] ?? 0,
            'balls' => $highestRunScorer['balls'] ?? 0,
            'fours' => $highestRunScorer['fours'] ?? 0,
            'sixes' => $highestRunScorer['sixes'] ?? 0,
            'strike_rate' => $highestRunScorer['strike_rate'] ?? 0
        ];
    }
}
