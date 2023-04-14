<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Player;
use App\Models\Score;
use App\Models\Squad;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function liveMatchList()
    {
        $matches = CricketMatch::with(['teamA', 'teamB'])->get();
        return view('pages.home.matchList', ['liveMatches' => $matches]);
    }
    public function liveMatch($matchId)
    {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            ->where('id', '=', $matchId)
            ->where('time', '<', $currentDate)
            ->first();
        $matchInInnings = Innings::where('match_id', $matchId)->first();
        if (!$match) {
            return redirect('/')->withDanger('match ' . $matchId . ' not found for score!');
        }
        if (!$matchInInnings) {
            return redirect('/')->withDanger('match ' . $matchId . ' not found for score!');
        }
        $firstBattingTeam = Innings::where('match_id', $matchId)
            ->where('innings', 1)
            ->select('battingTeam_id')
            ->first();
        $firstBowlingTeam = Innings::where('match_id', $matchId)
            ->where('innings', 1)
            ->select('bowlingTeam_id')
            ->first();
        $secondBattingTeam = Innings::where('match_id', $matchId)
            ->where('innings', 2)
            ->select('battingTeam_id')
            ->first();
        $secondBowlingTeam = Innings::where('match_id', $matchId)
            ->where('innings', 2)
            ->select('bowlingTeam_id')
            ->first();

        $firstBattingTeamName = Squad::where('team_id', $firstBattingTeam->battingTeam_id)->where('match_id', $matchId)->get('team_name');
        $firstBowlingTeamName = Squad::where('team_id', $firstBowlingTeam->bowlingTeam_id)->where('match_id', $matchId)->get('team_name');
        $secondBattingTeamName = Squad::where('team_id', $secondBattingTeam->battingTeam_id)->where('match_id', $matchId)->get('team_name');
        $secondBowlingTeamName = Squad::where('team_id', $secondBowlingTeam->bowlingTeam_id)->where('match_id', $matchId)->get('team_name');
        
        //first innings batting score calculation
        $firstBattingTeamScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $firstBattingTeam->battingTeam_id)->get();

        $firstTeamTotalWicket = $firstBattingTeamScore->sum('wicket');
        $firstTotalBall = $firstBattingTeamScore->sum('ball');
        $firstTotalOver = floor($firstTotalBall / 6) . '.' . ($firstTotalBall % 6);
        $firstTeamTotalFours = $firstBattingTeamScore->where('run', 4)->count();
        $firstTeamTotalSixes = $firstBattingTeamScore->where('run', 6)->count();

        $firstTeamTotalExtraRuns = 0;
        foreach ($firstBattingTeamScore as $ball) {
            $extraRun = 0;
            if (preg_match('/^(NB|WD|LB|B)(\d+)?$/i', $ball->extra, $matches)) {
                $extraRun = isset($matches[2]) ? (int)$matches[2] : 1;
            }
            $firstTeamTotalExtraRuns += $extraRun;
        }
        $firstTeamTotalRuns = $firstBattingTeamScore->sum('run') + $firstTeamTotalExtraRuns;
        // 1st innings individual run calculation
        $firstTeamIndividualScore = $firstBattingTeamScore->groupBy('batsman_id')
            ->map(function ($batsmanScores, $batsmanId) {
                $totalRuns = $batsmanScores->sum('run');
                $totalBalls = $batsmanScores->sum('ball');
                $totalFours = $batsmanScores->where('run', 4)->count();
                $totalSixes = $batsmanScores->where('run', 6)->count();
                $strikeRate = ($totalBalls > 0) ? round(($totalRuns / $totalBalls) * 100, 2) : 0;
                $bowlerName = '';
                if ($batsmanScores->where('wicket', 1)->isNotEmpty()) {
                    $bowlerId = $batsmanScores->where('wicket', 1)->pluck('bowler_id')->first();
                    $bowlerName = Squad::where('player_id', $bowlerId)->value('player_name');
                }
                return [
                    'bowlerName' => $bowlerName,
                    'batsman_id' => $batsmanId,
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                    'strike_rate' => $strikeRate,
                ];
            })
            ->mapWithKeys(function ($runBallTotal) {
                $playerName = Player::where('id', $runBallTotal['batsman_id'])->value('name');
                return [$playerName => $runBallTotal];
            });

        // 1st innings highest run scorer
        $firstHighestRunScorerData = $firstTeamIndividualScore->max(function ($batsman) {
            return $batsman['runs'];
        }) ?? 0;
        $firstHighestRunScorer = $firstTeamIndividualScore->filter(function ($batsman) use ($firstHighestRunScorerData) {
            return $batsman['runs'] == $firstHighestRunScorerData;
        })->first();
        $firstHighestRunScorerBalls = $firstHighestRunScorer['balls'] ?? 0;
        $firstHighestRunScorerRuns = $firstHighestRunScorer['runs'] ?? 0;
        $firstHighestRunScorerFours = $firstHighestRunScorer['fours'] ?? 0;
        $firstHighestRunScorerSixes = $firstHighestRunScorer['sixes'] ?? 0;
        $firstHighestRunScorerName = $firstTeamIndividualScore->search(function ($batsman) use ($firstHighestRunScorerData) {
            return $batsman['runs'] == $firstHighestRunScorerData;
        }) ?? 0;
        $firstHighestRunScorerStrikeRate = $firstHighestRunScorerBalls > 0 ? number_format(($firstHighestRunScorerRuns / $firstHighestRunScorerBalls) * 100, 2) : 0;
        $firstHighestRunScorer = [
            'name' => $firstHighestRunScorerName,
            'runs' => $firstHighestRunScorerRuns,
            'balls' => $firstHighestRunScorerBalls,
            'fours' => $firstHighestRunScorerFours,
            'sixes' => $firstHighestRunScorerSixes,
            'strike_rate' => $firstHighestRunScorerStrikeRate
        ];
        //first innings bowling score calculation
        $firstBowlingTeamScore = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $firstBowlingTeam->bowlingTeam_id)->get();

        $firstBowlingIndividualScore = $firstBowlingTeamScore->groupBy('bowler_id')
            ->map(function ($bowlerScores) {
                $totalRuns = $bowlerScores->sum(function ($ball) {
                    return intval($ball->run);
                });
                $totalBalls = $bowlerScores->sum('ball');
                $totalFours = $bowlerScores->where('run', 4)->count();
                $totalSixes = $bowlerScores->where('run', 6)->count();
                $totalWicket = $bowlerScores->where('wicket', 1)->count();
                $totalOvers = floor($totalBalls / 6) . '.' . ($totalBalls % 6);

                $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                    $extraRun = 0;
                    $noBallRun = 0;
                    $wideRun = 0;
                    $noBallCount = 0;
                    $wideBallCount = 0;
                    if (strpos($ball->extra, 'NB') !== false) {
                        $noBallRun += 1;
                        $noBallCount += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'WD') !== false) {
                        $wideRun += 1;
                        $wideBallCount += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'LB') !== false) {
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'B') !== false) {
                        $extraRun = (int)substr($ball->extra, 1);
                    }
                    $carry['extra_runs'] += $extraRun;
                    $carry['no_ball_runs'] += $noBallRun * $extraRun;
                    $carry['wide_runs'] += $wideRun * $extraRun;
                    $carry['no_ball_count'] += $noBallCount;
                    $carry['wide_ball_count'] += $wideBallCount;
                    return $carry;
                }, ['extra_runs' => 0, 'no_ball_runs' => 0, 'wide_runs' => 0, 'no_ball_count' => 0, 'wide_ball_count' => 0]);

                $totalExtra = $extraData['extra_runs'];
                $totalNoBallRuns = $extraData['no_ball_runs'];
                $totalWideRuns = $extraData['wide_runs'];
                $totalNoCount = $extraData['no_ball_count'];
                $totalWideCount = $extraData['wide_ball_count'];
                $totalRuns += $totalExtra;
                $economyRate = $totalBalls != 0 ? round($totalRuns / ($totalBalls / 6), 2) : ($totalRuns != 0 ? $totalRuns * 6 : 0);
                return [
                    'bowler_id' => $bowlerScores->first()->bowler_id,
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                    'wickets' => $totalWicket,
                    'totalExtra' => $totalExtra,
                    'totalNoBallRuns' => $totalNoBallRuns,
                    'totalWideRuns' => $totalWideRuns,
                    'totalNoCount' => $totalNoCount,
                    'totalWideCount' => $totalWideCount,
                    'economyRate' => $economyRate,
                    'totalOvers' => $totalOvers
                ];
            })
            ->mapWithKeys(function ($total, $bowlerId) {
                $player = Player::where('id', $bowlerId)->value('name');
                return [$player => $total];
            });
        //  first innings economical bowler 
        $firstMostEconomicalBowler = 0;
        $firstBowlingStats = $firstBowlingIndividualScore->toArray();
        if (empty($firstBowlingStats)) {
            $firstMostEconomicalBowler = [
                'name' => 0,
                'runs' => 0,
                'totalOvers' => 0,
                'economyRate' => 0,
                'wickets' => 0,
                'totalExtra' => 0,
                'totalNoBallRuns' => 0,
                'totalWideRuns' => 0,
                'totalNoCount' => 0,
                'totalWideCount' => 0
            ];
        } else {
            // sort and get the most economical bowler
            usort($firstBowlingStats, function ($a, $b) {
                if ($a['balls'] == 0 && $b['balls'] == 0) {
                    return 0;
                } elseif ($a['balls'] == 0) {
                    return 1;
                } elseif ($b['balls'] == 0) {
                    return -1;
                }
                return ($a['runs'] / $a['balls']) <=> ($b['runs'] / $b['balls']);
            });
            $firstMostEconomicalBowler = value($firstBowlingStats[0]);
            $bowlerName = Player::where('id', $firstMostEconomicalBowler['bowler_id'])->value('name');
            if ($bowlerName) {
                $firstMostEconomicalBowler['name'] = $bowlerName;
            } else {
                $firstMostEconomicalBowler['name'] = 0;
            }
        }
        // 2nd innings batting score calculation 
        $secondBattingTeamScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $secondBattingTeam->battingTeam_id)->get();
        $secondTeamTotalWicket = $secondBattingTeamScore->sum('wicket');
        $secondTotalBall = $secondBattingTeamScore->sum('ball');
        $secondTotalOver = floor($secondTotalBall / 6) . '.' . ($secondTotalBall % 6);
        $secondTeamTotalFours = $secondBattingTeamScore->where('run', 4)->count();
        $secondTeamTotalSixes = $secondBattingTeamScore->where('run', 6)->count();
        $secondTeamTotalExtraRuns = 0;
        foreach ($secondBattingTeamScore as $ball) {
            $extraRun = 0;
            if (preg_match('/^(NB|WD|LB|B)(\d+)?$/i', $ball->extra, $matches)) {
                $extraRun = isset($matches[2]) ? (int)$matches[2] : 1;
            }
            $secondTeamTotalExtraRuns += $extraRun;
        }
        $secondTeamTotalRuns = $secondBattingTeamScore->sum('run') + $secondTeamTotalExtraRuns;
        // 2nd innings individual run calculation
        $secondTeamIndividualScore = $secondBattingTeamScore->groupBy('batsman_id')
            ->map(function ($batsmanScores, $batsmanId) {
                $totalRuns = $batsmanScores->sum('run');
                $totalBalls = $batsmanScores->sum('ball');
                $totalFours = $batsmanScores->where('run', 4)->count();
                $totalSixes = $batsmanScores->where('run', 6)->count();
                $strikeRate = ($totalBalls > 0) ? round(($totalRuns / $totalBalls) * 100, 2) : 0;
                $bowlerName = '';
                if ($batsmanScores->where('wicket', 1)->isNotEmpty()) {
                    $bowlerId = $batsmanScores->where('wicket', 1)->pluck('bowler_id')->first();
                    $bowlerName = Squad::where('player_id', $bowlerId)->value('player_name');
                }
                return [
                    'bowlerName' => $bowlerName,
                    'batsman_id' => $batsmanId,
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                    'strike_rate' => $strikeRate,
                ];
            })
            ->mapWithKeys(function ($runBallTotal) {
                $playerName = Player::where('id', $runBallTotal['batsman_id'])->value('name');
                return [$playerName => $runBallTotal];
            });
        // 2nd innings highest run scorer
        $secondHighestRunScorerData = $secondTeamIndividualScore->max(function ($batsman) {
            return $batsman['runs'];
        }) ?? 0;
        $secondHighestRunScorer = $secondTeamIndividualScore->filter(function ($batsman) use ($secondHighestRunScorerData) {
            return $batsman['runs'] == $secondHighestRunScorerData;
        })->first();
        $secondHighestRunScorerBalls = $secondHighestRunScorer['balls'] ?? 0;
        $secondHighestRunScorerRuns = $secondHighestRunScorer['runs'] ?? 0;
        $secondHighestRunScorerFours = $secondHighestRunScorer['fours'] ?? 0;
        $secondHighestRunScorerSixes = $secondHighestRunScorer['sixes'] ?? 0;
        $secondHighestRunScorerName = $secondTeamIndividualScore->search(function ($batsman) use ($secondHighestRunScorerData) {
            return $batsman['runs'] == $secondHighestRunScorerData;
        }) ?? 0;
        $secondHighestRunScorerStrikeRate = $secondHighestRunScorerBalls > 0 ? number_format(($secondHighestRunScorerRuns / $secondHighestRunScorerBalls) * 100, 2) : 0;
        $secondHighestRunScorer = [
            'name' => $secondHighestRunScorerName,
            'runs' => $secondHighestRunScorerRuns,
            'balls' => $secondHighestRunScorerBalls,
            'fours' => $secondHighestRunScorerFours,
            'sixes' => $secondHighestRunScorerSixes,
            'strike_rate' => $secondHighestRunScorerStrikeRate
        ];
        //2nd innings bowling score calculation
        $secondBowlingTeamScore = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $secondBowlingTeam->bowlingTeam_id)->get();
        $secondBowlingIndividualScore = $secondBowlingTeamScore->groupBy('bowler_id')
            ->map(function ($bowlerScores) {
                $totalRuns = $bowlerScores->sum(function ($ball) {
                    return intval($ball->run);
                });
                $totalBalls = $bowlerScores->sum('ball');
                $totalFours = $bowlerScores->where('run', 4)->count();
                $totalSixes = $bowlerScores->where('run', 6)->count();
                $totalWicket = $bowlerScores->where('wicket', 1)->count();
                $totalOvers = floor($totalBalls / 6) . '.' . ($totalBalls % 6);
                $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                    $extraRun = 0;
                    $noBallRun = 0;
                    $wideRun = 0;
                    $noBallCount = 0;
                    $wideBallCount = 0;
                    if (strpos($ball->extra, 'NB') !== false) {
                        $noBallRun += 1;
                        $noBallCount += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'WD') !== false) {
                        $wideRun += 1;
                        $wideBallCount += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'LB') !== false) {
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'B') !== false) {
                        $extraRun = (int)substr($ball->extra, 1);
                    }
                    $carry['extra_runs'] += $extraRun;
                    $carry['no_ball_runs'] += $noBallRun * $extraRun;
                    $carry['wide_runs'] += $wideRun * $extraRun;
                    $carry['no_ball_count'] += $noBallCount;
                    $carry['wide_ball_count'] += $wideBallCount;
                    return $carry;
                }, ['extra_runs' => 0, 'no_ball_runs' => 0, 'wide_runs' => 0, 'no_ball_count' => 0, 'wide_ball_count' => 0]);
                $totalExtra = $extraData['extra_runs'];
                $totalNoBallRuns = $extraData['no_ball_runs'];
                $totalWideRuns = $extraData['wide_runs'];
                $totalNoCount = $extraData['no_ball_count'];
                $totalWideCount = $extraData['wide_ball_count'];
                $totalRuns += $totalExtra;
                $economyRate = $totalBalls != 0 ? round($totalRuns / ($totalBalls / 6), 2) : ($totalRuns != 0 ? $totalRuns * 6 : 0);
                return [
                    'bowler_id' => $bowlerScores->first()->bowler_id,
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                    'wickets' => $totalWicket,
                    'totalExtra' => $totalExtra,
                    'totalNoBallRuns' => $totalNoBallRuns,
                    'totalWideRuns' => $totalWideRuns,
                    'totalNoCount' => $totalNoCount,
                    'totalWideCount' => $totalWideCount,
                    'economyRate' => $economyRate,
                    'totalOvers' => $totalOvers
                ];
            })
            ->mapWithKeys(function ($total, $bowlerId) {
                $player = Player::where('id', $bowlerId)->value('name');
                return [$player => $total];
            });
        // second innings economical bowler 
        $secondMostEconomicalBowler = 0;
        $secondBowlingStats = $secondBowlingIndividualScore->toArray();
        if (empty($secondBowlingStats)) {
            $secondMostEconomicalBowler = [
                'name' => 0,
                'runs' => 0,
                'totalOvers' => 0,
                'economyRate' => 0,
                'wickets' => 0,
                'totalExtra' => 0,
                'totalNoBallRuns' => 0,
                'totalWideRuns' => 0,
                'totalNoCount' => 0,
                'totalWideCount' => 0
            ];
        } else {
            // sort and get the most economical bowler
            usort($secondBowlingStats, function ($a, $b) {
                if ($a['balls'] == 0 && $b['balls'] == 0) {
                    return 0;
                } elseif ($a['balls'] == 0) {
                    return 1;
                } elseif ($b['balls'] == 0) {
                    return -1;
                }
                return ($a['runs'] / $a['balls']) <=> ($b['runs'] / $b['balls']);
            });
            $secondMostEconomicalBowler = value($secondBowlingStats[0]);
            $bowlerName = Player::where('id', $secondMostEconomicalBowler['bowler_id'])->value('name');
            if ($bowlerName) {
                $secondMostEconomicalBowler['name'] = $bowlerName;
            } else {
                $secondMostEconomicalBowler['name'] = 0;
            }
        }

        $outBatsmanList = Score::where('match_id', $matchId)->where('wicket', 1)->pluck('batsman_id')->toArray();
        $matchTotalOver = CricketMatch::where('id', $matchId)->value('over');
        $firstTeamScoreLine = Score::where('battingTeam_id', $firstBattingTeam->battingTeam_id)
            ->where('match_id', $matchId)->get(['score_line']);
        $secondTeamScoreLine = Score::where('battingTeam_id', $secondBattingTeam->battingTeam_id)
            ->where('match_id', $matchId)->get(['score_line']);

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

        return view('pages.home.live', [
            'match' => $match,
            'outBatsmanList' => $outBatsmanList,
            'matchTotalOver' => $matchTotalOver,
            'firstBattingTeamName' => $firstBattingTeamName,
            'secondBattingTeamName' => $secondBattingTeamName,
            'firstBowlingTeamName' => $firstBowlingTeamName,
            'secondBowlingTeamName' => $secondBowlingTeamName,
            'inningsStatus' => $inningsStatus,
            //1st batting
            'firstTeamTotalRuns' => $firstTeamTotalRuns,
            'firstTeamTotalWicket' => $firstTeamTotalWicket,
            'firstTotalOver' => $firstTotalOver,
            'firstTeamTotalFours' => $firstTeamTotalFours,
            'firstTeamTotalSixes' => $firstTeamTotalSixes,
            'firstTeamIndividualScore' => $firstTeamIndividualScore,
            'firstTeamScoreLine' => $firstTeamScoreLine,
            'firstTeamTotalExtraRuns' => $firstTeamTotalExtraRuns,
            'firstHighestRunScorer' => $firstHighestRunScorer,

            // 1st bowling
            'firstBowlingIndividualScore' => $firstBowlingIndividualScore,
            'firstMostEconomicalBowler' => $firstMostEconomicalBowler,

            //2nd batting
            'secondTeamTotalRuns' => $secondTeamTotalRuns,
            'secondTeamTotalWicket' => $secondTeamTotalWicket,
            'secondTotalOver' => $secondTotalOver,
            'secondTeamTotalFours' => $secondTeamTotalFours,
            'secondTeamTotalSixes' => $secondTeamTotalSixes,
            'secondTeamIndividualScore' => $secondTeamIndividualScore,
            'secondTeamScoreLine' => $secondTeamScoreLine,
            'secondTeamTotalExtraRuns' => $secondTeamTotalExtraRuns,
            'secondHighestRunScorer' => $secondHighestRunScorer,

            // 2nd bowling
            'secondBowlingIndividualScore' => $secondBowlingIndividualScore,
            'secondMostEconomicalBowler' => $secondMostEconomicalBowler,
        ]);
    }
}
