<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Player;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Team;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function showAdminScore($matchId)
    {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            ->where('id', '=', $matchId)
            ->where('time', '<', $currentDate)
            ->first();
        $matchInInnings = Innings::where('match_id', $matchId)->first();

        if (!$match) {
            return redirect('dashboard')->withDanger('match id ' . $matchId . ' not found!');
        }
        if (!$matchInInnings) {
            return redirect('dashboard')->withDanger('match id ' . $matchId . ' not found for score!');
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
        $firstBattingSquad = Squad::where('team_id', $firstBattingTeam->battingTeam_id)->get();
        $firstBowlingSquad = Squad::where('team_id', $firstBowlingTeam->bowlingTeam_id)->get();
        $secondBattingSquad = Squad::where('team_id', $secondBattingTeam->battingTeam_id)->get();
        $secondBowlingSquad = Squad::where('team_id', $secondBowlingTeam->bowlingTeam_id)->get();

        //first innings batting score calculation
        $firstBattingTeamScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $firstBattingTeam->battingTeam_id)->get();

        //first innings total run calculation
        $firstTeamTotalRuns = $firstBattingTeamScore->reduce(function ($totalRuns, $ball) {
            $run = (int)$ball->run;
            if (strpos($ball->run, 'NB') !== false) {
                $run += (int)substr($ball->run, 2);
            } else if (strpos($ball->run, 'WD') !== false) {
                $run += (int)substr($ball->run, 2);
            } else if (strpos($ball->run, 'LB') !== false) {
                $run += (int)substr($ball->run, 2);
            } else if (strpos($ball->run, 'B') !== false) {
                $run += (int)substr($ball->run, 1);
            }
            return $totalRuns + $run;
        }, 0);

        $firstTeamTotalWicket = $firstBattingTeamScore->sum('wicket');
        $firstTotalOver = intval($firstBattingTeamScore->sum('ball') / 6);
        $firstTeamOverCarry = $firstBattingTeamScore->sum('ball') % 6;
        $firstTeamTotalFours = $firstBattingTeamScore->where('run', 4)->count();
        $firstTeamTotalSixes = $firstBattingTeamScore->where('run', 6)->count();

        // first innings extra run calculation
        $firstTeamTotalExtraRuns = $firstBattingTeamScore->sum(function ($ball) {
            $extraRun = 0;
            if (strpos($ball->extra, 'NB') !== false) {
                $extraRun = (int)substr($ball->extra, 2);
            } else if (strpos($ball->extra, 'WD') !== false) {
                $extraRun = (int)substr($ball->extra, 2);
            } else if (strpos($ball->extra, 'LB') !== false) {
                $extraRun = (int)substr($ball->extra, 2);
            } else if (strpos($ball->extra, 'B') !== false) {
                $extraRun = (int)substr($ball->extra, 1);
            }
            return $extraRun;
        });

        // 1st innings individual run calculation
        $firstTeamIndividualScore = $firstBattingTeamScore->filter(function ($ball) {
            return $ball->batsman_id != 0;
        })->groupBy('batsman_id')
            ->map(function ($batsmanScores) {
                $totalRuns = $batsmanScores->sum('run');
                $totalBalls = $batsmanScores->sum('ball');
                $totalFours = $batsmanScores->where('run', 4)->count();
                $totalSixes = $batsmanScores->where('run', 6)->count();
                return [
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                ];
            })
            ->mapWithKeys(function ($runBallTotal, $batsmanId) {
                $playerName = Player::where('id', $batsmanId)->value('name');
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
                $economyRate = round($totalRuns / ($totalBalls / 6), 2);
                $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                    $extraRun = 0;
                    $noBallRun = 0;
                    $wideRun = 0;
                    $noBallCount = 0;
                    $wideBallCount = 0;
                    if (strpos($ball->run, 'NB') !== false) {
                        $noBallRun += 1;
                        $noBallCount += 1;
                        $extraRun = (int)substr($ball->run, 2);
                    } else if (strpos($ball->run, 'WD') !== false) {
                        $wideRun += 1;
                        $wideBallCount += 1;
                        $extraRun = (int)substr($ball->run, 2);
                    } else if (strpos($ball->run, 'LB') !== false) {
                        $extraRun = (int)substr($ball->run, 2);
                    } else if (strpos($ball->run, 'B') !== false) {
                        $extraRun = (int)substr($ball->run, 1);
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
                'totalWideRuns' => 0
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

        // second innings batting score calculation 
        $secondBattingTeamScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $secondBattingTeam->battingTeam_id)->get();
        //first innings total run calculation
        $secondTeamTotalRuns = $secondBattingTeamScore->reduce(function ($totalRuns, $ball) {
            $run = (int)$ball->run;
            if (strpos($ball->run, 'NB') !== false) {
                $run += (int)substr($ball->run, 2);
            } else if (strpos($ball->run, 'WD') !== false) {
                $run += (int)substr($ball->run, 2);
            } else if (strpos($ball->run, 'LB') !== false) {
                $run += (int)substr($ball->run, 2);
            } else if (strpos($ball->run, 'B') !== false) {
                $run += (int)substr($ball->run, 1);
            }
            return $totalRuns + $run;
        }, 0);


        $secondTeamTotalWicket = $secondBattingTeamScore->sum('wicket');
        $secondTotalOver = intval($secondBattingTeamScore->sum('ball') / 6);
        $secondTeamOverCarry = $secondBattingTeamScore->sum('ball') % 6;
        $secondTeamTotalFours = $secondBattingTeamScore->where('run', 4)->count();
        $secondTeamTotalSixes = $secondBattingTeamScore->where('run', 6)->count();
        $secondTeamTotalExtraRuns = $secondBattingTeamScore->sum(function ($ball) {
            $extraRun = 0;
            if (strpos($ball->extra, 'NB') !== false) {
                $extraRun = (int)substr($ball->extra, 2);
            } else if (strpos($ball->extra, 'WD') !== false) {
                $extraRun = (int)substr($ball->extra, 2);
            } else if (strpos($ball->extra, 'LB') !== false) {
                $extraRun = (int)substr($ball->extra, 2);
            } else if (strpos($ball->extra, 'B') !== false) {
                $extraRun = (int)substr($ball->extra, 1);
            }
            return $extraRun;
        });

        $secondTeamIndividualScore = $secondBattingTeamScore->filter(function ($ball) {
            return $ball->batsman_id != 0;
        })->groupBy('batsman_id')
            ->map(function ($batsmanScores) {
                $totalRuns = $batsmanScores->sum('run');
                $totalBalls = $batsmanScores->sum('ball');
                $totalFours = $batsmanScores->where('run', 4)->count();
                $totalSixes = $batsmanScores->where('run', 6)->count();
                return [
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                ];
            })
            ->mapWithKeys(function ($runBallTotal, $batsmanId) {
                $playerName = Player::where('id', $batsmanId)->value('name');
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
        $firstTeamScoreLine = Score::where('battingTeam_id', $firstBattingTeam->battingTeam_id)
            ->where('match_id', $matchId)->get(['run']);

        $secondTeamScoreLine = Score::where('battingTeam_id', $secondBattingTeam->battingTeam_id)
            ->where('match_id', $matchId)->get(['run']);


        //first innings bowling score calculation
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
                $economyRate = round($totalRuns / ($totalBalls / 6), 2);
                $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                    $extraRun = 0;
                    $noBallRun = 0;
                    $wideRun = 0;
                    $noBallCount = 0;
                    $wideBallCount = 0;
                    if (strpos($ball->run, 'NB') !== false) {
                        $noBallRun += 1;
                        $noBallCount += 1;
                        $extraRun = (int)substr($ball->run, 2);
                    } else if (strpos($ball->run, 'WD') !== false) {
                        $wideRun += 1;
                        $wideBallCount += 1;
                        $extraRun = (int)substr($ball->run, 2);
                    } else if (strpos($ball->run, 'LB') !== false) {
                        $extraRun = (int)substr($ball->run, 2);
                    } else if (strpos($ball->run, 'B') !== false) {
                        $extraRun = (int)substr($ball->run, 1);
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
                'totalWideRuns' => 0
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
        $runningInningsStatus = Innings::where('match_id', $matchId)->get();
        $inningsOne = 0;
        $inningsTwo = 0;
        foreach ($runningInningsStatus as $innings) {
            if ($innings->innings == 1) {
                $inningsOne = $innings->status;
            } elseif ($innings->innings == 1) {
                $inningsTwo = $innings->status;
            }
        }
        $inningsStatus = [
            'inningsOne' => $inningsOne,
            'inningsTwo' => $inningsTwo
        ];
        $outBatsmanList = Score::where('match_id', $matchId)->where('wicket', 1)->pluck('batsman_id')->toArray();
        return view(
            'pages.scores.scoreDashboard',
            [
                'outBatsmanList' => $outBatsmanList,
                'match' => $match,
                'inningsStatus' => $inningsStatus,
                'firstBattingSquad' => $firstBattingSquad,
                'firstBowlingSquad' => $firstBowlingSquad,
                'secondBattingSquad' => $secondBattingSquad,
                'secondBowlingSquad' => $secondBowlingSquad,

                //1st batting
                'firstTeamTotalRuns' => $firstTeamTotalRuns,
                'firstTeamTotalWicket' => $firstTeamTotalWicket,
                'firstTotalOver' => $firstTotalOver,
                'firstTeamTotalFours' => $firstTeamTotalFours,
                'firstTeamTotalSixes' => $firstTeamTotalSixes,
                'firstTeamOverCarry' => $firstTeamOverCarry,
                'firstTeamIndividualScore' => $firstTeamIndividualScore,
                'firstTeamScoreLine' => $firstTeamScoreLine,
                'firstTeamTotalExtraRuns' => $firstTeamTotalExtraRuns,
                'firstHighestRunScorer' => $firstHighestRunScorer,

                // 1st bowling
                'firstBowlingIndividualScore' => $firstBowlingIndividualScore,
                'firstMostEconomicalBowler' => $firstMostEconomicalBowler,

                'secondTeamTotalRuns' => $secondTeamTotalRuns,
                'secondTeamTotalWicket' => $secondTeamTotalWicket,
                'secondTotalOver' => $secondTotalOver,
                'secondTeamTotalFours' => $secondTeamTotalFours,
                'secondTeamTotalSixes' => $secondTeamTotalSixes,
                'secondTeamOverCarry' => $secondTeamOverCarry,
                'secondTeamIndividualScore' => $secondTeamIndividualScore,
                'secondTeamScoreLine' => $secondTeamScoreLine,
                'secondTeamTotalExtraRuns' => $secondTeamTotalExtraRuns,
                'secondHighestRunScorer' => $secondHighestRunScorer,

                // 2nd bowling
                'secondBowlingIndividualScore' => $secondBowlingIndividualScore,
                'secondMostEconomicalBowler' => $secondMostEconomicalBowler,
            ]
        );
    }
    public function updateScore(Request $request)
    {
        $matchId = $request->matchId;
        $bowlerId = $request->bowler_id[0];
        $batsmanId = $request->batsman_id[0];
        $battingTeamId = $request->battingTeamId[0];
        $bowlingTeamId = $request->bowlingTeamId[0];
        $run = $request->run;
        $wicket = $request->wicket;
        $extra = $request->extra;

        $previousScore = Score::where('match_id', $matchId)->orderBy('id', 'desc')->first();
        $batsmanOut = $previousScore && $previousScore->wicket == 1 && $previousScore->batsman_id == $batsmanId;
        if ($batsmanOut) {
            $playerName = Squad::where('player_id', $batsmanId)->value('player_name');
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger($playerName . ' is already out!');
        }


        $ball = 0;
        if ($run != null) {
            $extra = 0;
            $wicket = 0;
            $ball = 1;
        } elseif ($extra != null) {
            $wicket = 0;
            $batsmanId = 0;
            if (strpos($extra, 'LB') === 0 || strpos($extra, 'B') === 0) {
                $ball = 1;
                $run = $extra;
            } else {
                $run = $extra;
                $extra = $extra;
                $ball = 0;
            }
        } elseif ($wicket == 1) {
            $run = 0;
            $extra = 0;
            $ball = 1;
        }
        $score = new Score;
        $score->match_id = $matchId;
        $score->bowler_id = $bowlerId;
        $score->batsman_id = $batsmanId;
        $score->battingTeam_id = $battingTeamId;
        $score->bowlingTeam_id = $bowlingTeamId;
        $score->run = $run;
        $score->wicket = $wicket;
        $score->extra = $extra;
        $score->ball = $ball;
        $score->save();

        // $newScore = Score::where('match_id', $matchId)->get();
        // $totalRuns = $newScore->sum('run');
        // $totalWicket = $newScore->sum('wicket');
        // $totalOver = intval($newScore->sum('ball') / 6);
        // $overCarry = $newScore->sum('ball') % 6;

        // $individualRuns = $newScore->groupBy('batsman_id')
        //     ->map(function ($batsmanScores) {
        //         return $batsmanScores->sum('run');
        //     });
        // $individualRuns = $individualRuns->mapWithKeys(function ($runTotal, $batsmanId) {
        //     $playerName = Player::where('id', $batsmanId)->value('name');
        //     return [$playerName => $runTotal];
        // });
        $playerName = Squad::where('player_id', $batsmanId)->value('player_name');
        if ($wicket == 1) {
            $message = $playerName . ' is out!';
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger($message);
        } else {
            $message = $run . ' runs by ' . $playerName;
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withSuccess($message);
        }




        // $squadPlayers = Squad::where('match_id', $matchId)->get();
        // $battingSquad = array();
        // $bowlingSquad = array();
        // foreach ($squadPlayers as $player) {
        //     if ($player->role == 'bat') {
        //         $battingSquad[] = array(
        //             'id' => $player->team_id,
        //             'name' => $player->team_name,
        //             'player_id' => $player->player_id,
        //             'player_name' => $player->player_name
        //         );
        //     } else if ($player->role == 'ball') {
        //         $bowlingSquad[] = array(
        //             'id' => $player->team_id,
        //             'name' => $player->team_name,
        //             'player_id' => $player->player_id,
        //             'player_name' => $player->player_name
        //         );
        //     }
        // }
    }
}
