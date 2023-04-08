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

        $firstTeamTotalRuns = $firstBattingTeamScore->sum('run');
        $firstTeamTotalWicket = $firstBattingTeamScore->sum('wicket');
        $firstTotalOver = intval($firstBattingTeamScore->sum('ball') / 6);
        $firstTeamOverCarry = $firstBattingTeamScore->sum('ball') % 6;
        $firstTeamTotalFours = $firstBattingTeamScore->where('run', 4)->count();
        $firstTeamTotalSixes = $firstBattingTeamScore->where('run', 6)->count();

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

        $firstTeamIndividualScore = $firstBattingTeamScore->groupBy('batsman_id')
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
        });
        $firstHighestRunScorer = $firstTeamIndividualScore->filter(function ($batsman) use ($firstHighestRunScorerData) {
            return $batsman['runs'] == $firstHighestRunScorerData;
        })->first();
        $firstHighestRunScorerBalls = $firstHighestRunScorer['balls'];
        $firstHighestRunScorerRuns = $firstHighestRunScorer['runs'];
        $firstHighestRunScorerFours = $firstHighestRunScorer['fours'];
        $firstHighestRunScorerSixes = $firstHighestRunScorer['sixes'];
        $firstHighestRunScorerName = $firstTeamIndividualScore->search(function ($batsman) use ($firstHighestRunScorerData) {
            return $batsman['runs'] == $firstHighestRunScorerData;
        });
        $firstHighestRunScorerStrikeRate = number_format(($firstHighestRunScorerRuns / $firstHighestRunScorerBalls) * 100, 2);
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
                $totalRuns = $bowlerScores->sum('run');
                $totalBalls = $bowlerScores->sum('ball');
                $totalFours = $bowlerScores->where('run', 4)->count();
                $totalSixes = $bowlerScores->where('run', 6)->count();
                $totalWicket = $bowlerScores->where('wicket', 1)->count();
                $totalOvers = floor($totalBalls / 6) . '.' . ($totalBalls % 6);
                $economyRate = round($totalRuns / ($totalBalls / 6), 2);
                // $totalExtra = $bowlerScores->sum('extra');
                $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                    $extraRun = 0;
                    $noBallRun = 0;
                    $wideRun = 0;
                    if (strpos($ball->extra, 'NB') !== false) {
                        $noBallRun += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'WD') !== false) {
                        $wideRun += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'LB') !== false) {
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'B') !== false) {
                        $extraRun = (int)substr($ball->extra, 1);
                    }
                    // $carry['extra_runs'] += $extraRun; // it returns 5 run. but 1 need ball count (1 no ball 5 run)
                    // $carry['no_ball_runs'] += $noBallRun * $extraRun;
                    // $carry['wide_runs'] += $wideRun * $extraRun;

                    $carry['extra_runs'] += $extraRun;
                    $carry['no_ball_runs'] += $noBallRun;
                    $carry['wide_runs'] += $wideRun;
                    return $carry;
                }, ['extra_runs' => 0, 'no_ball_runs' => 0, 'wide_runs' => 0]);

                $totalExtra = $extraData['extra_runs'];
                $totalNoBallRuns = $extraData['no_ball_runs'];
                $totalWideRuns = $extraData['wide_runs'];

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
                    'economyRate' => $economyRate,
                    'totalOvers' => $totalOvers
                ];
            })
            ->mapWithKeys(function ($total, $bowlerId) {
                $player = Player::where('id', $bowlerId)->value('name');
                return [$player => $total];
            });

        $firstBowlingStats = $firstBowlingIndividualScore->toArray();
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
        $firstMostEconomicalBowler['name'] = $bowlerName;

        // $firstMostEconomicalBowlerName = Player::find($firstMostEconomicalBowlerId)->name;

        // $firstMostEconomicalBowlerData = [
        //     "name" => $firstMostEconomicalBowlerName,
        //     "runs" => $firstMostEconomicalBowler['runs'],
        //     "overs" => $firstMostEconomicalBowler['totalOvers'],
        //     "fours" => $firstMostEconomicalBowler['fours'],
        //     "sixes" => $firstMostEconomicalBowler['sixes'],
        //     "economy_rate" => $firstMostEconomicalBowler['economyRate']
        // ];

        // dd($firstMostEconomicalBowlerData);





        // second innings batting score calculation 
        $secondBattingTeamScore = Score::where('match_id', $matchId)
            ->where('battingTeam_id', $secondBattingTeam->battingTeam_id)->get();
        $secondTeamTotalRuns = $secondBattingTeamScore->sum('run');
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

        $secondTeamIndividualScore = $secondBattingTeamScore->groupBy('batsman_id')
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

        $firstTeamScoreLine = Score::where('battingTeam_id', $firstBattingTeam->battingTeam_id)
            ->where('match_id', $matchId)->get(['run']);

        $secondTeamScoreLine = Score::where('battingTeam_id', $secondBattingTeam->battingTeam_id)
            ->where('match_id', $matchId)->get(['run']);


        //second innings bowling score calculation
        $secondBowlingTeamScore = Score::where('match_id', $matchId)
            ->where('bowlingTeam_id', $secondBowlingTeam->bowlingTeam_id)->get();

        $secondBowlingIndividualScore = $secondBowlingTeamScore->groupBy('bowler_id')
            ->map(function ($bowlerScores) {
                $totalRuns = $bowlerScores->sum('run');
                $totalBalls = $bowlerScores->sum('ball');
                $totalFours = $bowlerScores->where('run', 4)->count();
                $totalSixes = $bowlerScores->where('run', 6)->count();
                $totalWicket = $bowlerScores->where('wicket', 1)->count();
                $totalOvers = floor($totalBalls / 6) . '.' . ($totalBalls % 6);
                $economyRate = round($totalRuns / ($totalBalls / 6), 2);
                // $totalExtra = $bowlerScores->sum('extra');
                $extraData = $bowlerScores->reduce(function ($carry, $ball) {
                    $extraRun = 0;
                    $noBallRun = 0;
                    $wideRun = 0;
                    if (strpos($ball->extra, 'NB') !== false) {
                        $noBallRun += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'WD') !== false) {
                        $wideRun += 1;
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'LB') !== false) {
                        $extraRun = (int)substr($ball->extra, 2);
                    } else if (strpos($ball->extra, 'B') !== false) {
                        $extraRun = (int)substr($ball->extra, 1);
                    }
                    $carry['extra_runs'] += $extraRun;
                    $carry['no_ball_runs'] += $noBallRun;
                    $carry['wide_runs'] += $wideRun;
                    return $carry;
                }, ['extra_runs' => 0, 'no_ball_runs' => 0, 'wide_runs' => 0]);

                $totalExtra = $extraData['extra_runs'];
                $totalNoBallRuns = $extraData['no_ball_runs'];
                $totalWideRuns = $extraData['wide_runs'];
                return [
                    'runs' => $totalRuns,
                    'balls' => $totalBalls,
                    'fours' => $totalFours,
                    'sixes' => $totalSixes,
                    'wickets' => $totalWicket,
                    'totalExtra' => $totalExtra,
                    'totalNoBallRuns' => $totalNoBallRuns,
                    'totalWideRuns' => $totalWideRuns,
                    'economyRate' => $economyRate,
                    'totalOvers' => $totalOvers
                ];
            })
            ->mapWithKeys(function ($total, $bowlerId) {
                $player = Player::where('id', $bowlerId)->value('name');
                return [$player => $total];
            });

        return view(
            'pages.scores.scoreDashboard',
            [
                'match' => $match,
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

                // 2nd bowling
                'secondBowlingIndividualScore' => $secondBowlingIndividualScore
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
        // have to add teamId

        $run = $request->run;
        $wicket = $request->wicket;
        $extra = $request->extra;
        if (!$extra) {
            $extra = 0;
        }

        if (!$wicket) {
            $wicket = 0;
        }
        $score = new Score();
        $score->match_id = $matchId;
        $score->run = $run;
        if (!$extra) {
            $score->ball = 1;
        } else {
            $score->ball = 0;
        }
        $score->batsman_id = $batsmanId;
        $score->bowler_id = $bowlerId;
        $score->extra = $extra;
        $score->wicket = $wicket;
        $score->battingTeam_id = $battingTeamId;
        $score->bowlingTeam_id = $bowlingTeamId;
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
        return redirect()->route('get.live.match.score', ['id' => $matchId])
            ->withSuccess($run . ' runs by ' . $playerName);



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
