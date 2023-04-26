<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Score;
use App\Models\Squad;
use App\Services\BattingService;
use App\Services\BowlingService;
use App\Services\MatchService;
use App\Services\ScoreService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScoreController extends Controller
{
    public function showAdminScore($matchId)
    {
        try {
            $MatchService = new MatchService();
            $MatchServiceData = $MatchService->getMatchData($matchId);
            $match = $MatchServiceData['match'];
            $firstBattingSquad =  $MatchServiceData['firstBattingSquad'];
            $firstBowlingSquad =  $MatchServiceData['firstBowlingSquad'];

            $ScoreService = new ScoreService();
            $innings1BattingScore  = $ScoreService->inningsTotalScore($matchId, $firstBattingSquad[0]->team_id);
            $innings2BattingScore  = $ScoreService->inningsTotalScore($matchId, $firstBowlingSquad[0]->team_id);

            $BattingService = new BattingService();
            $firstTeamIndividualScore = $BattingService->individualScore($innings1BattingScore['totalScore']);
            $secondTeamIndividualScore = $BattingService->individualScore($innings2BattingScore['totalScore']);

            $firstHighestRunScorer = $BattingService->highestRunScorer($firstTeamIndividualScore);
            $secondHighestRunScorer = $BattingService->highestRunScorer($secondTeamIndividualScore);

            $BowlingService = new BowlingService();
            $firstBowlingIndividualScore = $BowlingService->individualScore($matchId, $firstBowlingSquad[0]->team_id);
            $secondBowlingIndividualScore = $BowlingService->individualScore($matchId, $firstBattingSquad[0]->team_id);

            $firstMostEconomicalBowler = $BowlingService->mostEconomicalBowler($firstBowlingIndividualScore);
            $secondMostEconomicalBowler = $BowlingService->mostEconomicalBowler($secondBowlingIndividualScore);

            $MatchOtherData = $MatchService->matchOtherData($matchId,  $firstBattingSquad[0]->team_id, $firstBowlingSquad[0]->team_id);
            $outBatsmanList = $MatchOtherData['outBatsmanList'];
            $inningsStatus = $MatchOtherData['inningsStatus'];
            $firstTeamScoreLine = $MatchOtherData['firstTeamScoreLine'];
            $secondTeamScoreLine = $MatchOtherData['secondTeamScoreLine'];

            return view(
                'pages.scores.scoreDashboard',
                [
                    'match' => $match,
                    'outBatsmanList' => $outBatsmanList,
                    'inningsStatus' => $inningsStatus,
                    'firstTeamScoreLine' => $firstTeamScoreLine,
                    'secondTeamScoreLine' => $secondTeamScoreLine,

                    'firstBattingSquad' => $firstBattingSquad,
                    'firstBowlingSquad' => $firstBowlingSquad,
                    'secondBattingSquad' => $firstBowlingSquad,
                    'secondBowlingSquad' => $firstBattingSquad,

                    'firstBattingTeamName' => $firstBattingSquad[0]->team_name,
                    'firstBowlingTeamName' => $firstBowlingSquad[0]->team_name,

                    'innings1BattingScore' => $innings1BattingScore,
                    'innings2BattingScore' => $innings2BattingScore,

                    'firstTeamIndividualScore' => $firstTeamIndividualScore,
                    'secondTeamIndividualScore' => $secondTeamIndividualScore,

                    'firstHighestRunScorer' => $firstHighestRunScorer,
                    'secondHighestRunScorer' => $secondHighestRunScorer,

                    'firstBowlingIndividualScore' => $firstBowlingIndividualScore,
                    'firstMostEconomicalBowler' => $firstMostEconomicalBowler,

                    'secondBowlingIndividualScore' => $secondBowlingIndividualScore,
                    'secondMostEconomicalBowler' => $secondMostEconomicalBowler,
                ]
            );
        } catch (Exception $e) {
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
    public function updateScore(Request $request)
    {
        DB::beginTransaction();
        try {
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
            DB::commit();
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
                    ->withDanger("extra {$extraRun} run ({$extra}), " . 'bowler ' . $bowlerName . ', over ' . $bowlerOverCount . '/(' . $totalOverCount . ')');
            }
            return redirect()->route('get.live.match.score', ['id' => $matchId])
                ->withSuccess($run . " run by " . $batsmanName . ', bowler ' . $bowlerName . ', over ' . $bowlerOverCount . '/(' . $totalOverCount . ')');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
    public function updateInnings($matchId)
    {
        try{
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
        }catch (Exception $e){
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
}
