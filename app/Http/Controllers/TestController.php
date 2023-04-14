<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Player;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Team;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function showAdminScore($matchId)
    {
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            ->where('id', '=', $matchId)
            ->where('time', '<', $currentDate)
            ->first();

        $matchInInnings = Innings::where('match_id', $matchId)->first();
        if (!$match || !$matchInInnings) {
            return redirect('dashboard')->withDanger('Match ' . $matchId . ' not found!');
        }

        $TeamData = Innings::where('match_id', $matchId)
            ->whereIn('innings', [1, 2])
            ->select('battingTeam_id', 'bowlingTeam_id')
            ->get();

        $battingTeams = $TeamData->pluck('battingTeam_id')->toArray();
        $bowlingTeams = $TeamData->pluck('bowlingTeam_id')->toArray();



        $firstBattingSquad = Squad::where('team_id', $battingTeams[0])->where('match_id', $matchId)->get();
        $firstBowlingSquad = Squad::where('team_id', $bowlingTeams[0])->where('match_id', $matchId)->get();
        $secondBattingSquad = Squad::where('team_id', $battingTeams[1])->where('match_id', $matchId)->get();
        $secondBowlingSquad = Squad::where('team_id', $bowlingTeams[1])->where('match_id', $matchId)->get();

        //first innings batting score calculation
        $firstBattingTeamScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $battingTeams[0])->get();

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
            ->where('bowlingTeam_id', $bowlingTeams[0])->get();

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
            ->where('battingTeam_id', $battingTeams[1])->get();

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
                return [
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
            ->where('bowlingTeam_id', $bowlingTeams[1])->get();
            
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
        $firstTeamScoreLine = Score::where('battingTeam_id', $battingTeams[0])
            ->where('match_id', $matchId)->get(['score_line']);
        $secondTeamScoreLine = Score::where('battingTeam_id', $battingTeams[1])
            ->where('match_id', $matchId)->get(['score_line']);
        return view(
            'pages.scores.scoreDashboard',
            [
                'match' => $match,
                'outBatsmanList' => $outBatsmanList,
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
            ]
        );
    }
    public function updateScore(Request $request)
    {
        if (empty($request->bowler_id) && empty($request->batsman_id)) {
            $message = 'Select 1 batsman and 1 bowler!';
        } else if (empty($request->bowler_id)) {
            $message = 'Select 1 bowler!';
        } else if (empty($request->batsman_id)) {
            $message = 'Select 1 batsman!';
        } else if (count($request->bowler_id) > 1 && count($request->batsman_id) > 1) {
            $message = 'Select only 1 bowler and 1 batsman!';
        } else if (count($request->bowler_id) > 1) {
            $message = 'Select only 1 bowler!';
        } else if (count($request->batsman_id) > 1) {
            $message = 'Select only 1 batsman!';
        }
        if (isset($message)) {
            return redirect()->route('get.live.match.score', ['id' => $request->matchId])
                ->withDanger($message);
        }
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
        $scoreLine = 0;
        if ($run != null) {
            $extra = 0;
            $wicket = 0;
            $ball = 1;
            $scoreLine = $run;
        } elseif ($extra != null) {
            $wicket = 0;
            $scoreLine = $extra;
            $run = 0;
            if (strpos($extra, 'LB') === 0 || strpos($extra, 'B') === 0) {
                $ball = 1;
            } else {
                $ball = 0;
            }
        } elseif ($wicket == 1) {
            $run = 0;
            $extra = 0;
            $ball = 1;
            $scoreLine = 'W';
        }
        $battingTeam = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $request->battingTeamId[0])->get();
        $totalWicket = $battingTeam->where('wicket', 1)->count();
        $matchTotalBall = CricketMatch::where('id', $matchId)->value('over') * 6;
        $playedBall = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $bowlingTeamId)
            ->sum('ball');
        $remainingBall = $matchTotalBall - $playedBall;
        if ($remainingBall <= 0 && $totalWicket >= 10) {
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger('all out! finished over! match end!');
        } else if ($totalWicket >= 10) {
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger('all out. match end!');
        } else if ($remainingBall <= 0) {
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger('finished over! match end!');
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
        $score->score_line = $scoreLine;
        $score->save();

        $bowlerBalls = Score::where('match_id', $matchId)
            ->where('bowler_id', $bowlerId)
            ->where('ball', 1)
            ->count();
        $totalBalls = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $bowlingTeamId)
            ->sum('ball');
        $totalOverCount = floor($totalBalls / 6) . '.' . ($totalBalls % 6);
        $bowlerOverCount = floor($bowlerBalls / 6) . '.' . ($bowlerBalls % 6);

        $bowlerName = Squad::where('player_id', $bowlerId)->value('player_name');
        $batsmanName = Squad::where('player_id', $batsmanId)->value('player_name');
        if ($wicket == 1) {
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger($batsmanName . ' is out by ' . $bowlerName . ', over ' . $bowlerOverCount . '/(' . $totalOverCount . ')');
        }
        if ($extra != null) {
            $extraRun = 0;
            $extraType1 = strtoupper(substr($extra, 0, 2));
            $extraType2 = strtoupper(substr($extra, 0, 1));
            if (in_array($extraType1, ['NB', 'WD', 'LB'])) {
                $extraRun = (int)substr($extra, 2);
            } else if ($extraType2 == 'B') {
                $extraRun = (int)substr($extra, 1);
            }
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withDanger("extra {$extraRun} {$extra} run, " . 'bowler ' . $bowlerName . ', over ' . $bowlerOverCount . '/(' . $totalOverCount . ')');
        }
        return redirect()->route('get.live.match.score', ['id' => $matchId])
            ->withSuccess($run . " by " . $batsmanName . ', bowler ' . $bowlerName . ', over ' . $bowlerOverCount . '/(' . $totalOverCount . ')');
    }
    public function updateInnings($matchId)
    {
        $innings1 = Innings::where('match_id', $matchId)->where('innings', 1)->first();
        $innings2 = Innings::where('match_id', $matchId)->where('innings', 2)->first();

        // ball calculation
        $matchTotalBall = CricketMatch::where('id', $matchId)->value('over') * 6;
        $innings1totalBalls = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $innings1->bowlingTeam_id)
            ->sum('ball');
        $innings2totalBalls = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $innings2->bowlingTeam_id)
            ->sum('ball');

        // wicket calculation
        $innings1battingTeam = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $innings1->battingTeam_id)->get();
        $innings1totalWicket = $innings1battingTeam->where('wicket', 1)->count();
        $innings2battingTeam = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $innings2->battingTeam_id)->get();
        $innings2totalWicket = $innings2battingTeam->where('wicket', 1)->count();

        if ($innings1 && $innings2) {
            if ($innings1->status == 1) {
                if ($innings1totalBalls >= $matchTotalBall || $innings1totalWicket >= 10) {
                    $innings1->status = 2;
                    $innings1->save();
                    $innings2->status = 1;
                    $innings2->save();
                    return redirect()->route('get.live.match.score', ['id' => $matchId])->withSuccess('2nd innings start!');
                } else {
                    return redirect()->route('get.live.match.score', ['id' => $matchId])->withDanger('1st innings not finished yet!');
                }
            } else if ($innings2->status == 1) {
                if ($innings2totalBalls >= $matchTotalBall || $innings2totalWicket >= 10) {
                    $innings2->status = 2;
                    $innings2->save();
                    $match = CricketMatch::find($matchId);
                    $match->status = 'finished';
                    $match->save();
                    return redirect()->route('dashboard')->withSuccess('match end!');
                } else {
                    return redirect()->route('get.live.match.score', ['id' => $matchId])->withDanger('2nd innings not finished yet!');
                }
            }
        }
    }
}
