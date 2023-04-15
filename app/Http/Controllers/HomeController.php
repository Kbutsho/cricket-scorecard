<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Score;
use App\Services\BattingService;
use App\Services\BowlingService;
use App\Services\MatchService;
use App\Services\ScoreService;
use Exception;

class HomeController extends Controller
{
    public function liveMatchList()
    {
        try {
            $matches = CricketMatch::with(['teamA', 'teamB'])->get();
            return view('pages.home.matchList', ['liveMatches' => $matches]);
        } catch (Exception $error) {
            return redirect('/')->withDanger($error->getMessage());
        }
    }
    public function liveMatch($matchId)
    {
        try {
            $MatchService = new MatchService();
            $MatchServiceData = $MatchService->getMatchData($matchId);
            $match = $MatchServiceData['match'];
            $firstBattingSquad = $MatchServiceData['firstBattingSquad'];
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
            $secondBowlingIndividualScore = $BowlingService->individualScore($matchId, $firstBattingSquad[0]->team_id); // firstBattingSquad = secondBowlingSquad

            $firstMostEconomicalBowler = $BowlingService->mostEconomicalBowler($firstBowlingIndividualScore);
            $secondMostEconomicalBowler = $BowlingService->mostEconomicalBowler($secondBowlingIndividualScore);

            $MatchOtherData = $MatchService->matchOtherData($matchId,  $firstBattingSquad[0]->team_id, $firstBowlingSquad[0]->team_id);
            $outBatsmanList = $MatchOtherData['outBatsmanList'];
            $inningsStatus = $MatchOtherData['inningsStatus'];
            $firstTeamScoreLine = $MatchOtherData['firstTeamScoreLine'];
            $secondTeamScoreLine = $MatchOtherData['secondTeamScoreLine'];

            return view('pages.home.live', [
                'match' => $match,
                'inningsStatus' => $inningsStatus,
                'outBatsmanList' => $outBatsmanList,
                'firstTeamScoreLine' => $firstTeamScoreLine,
                'secondTeamScoreLine' => $secondTeamScoreLine,

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
            ]);
        } catch (Exception $e) {
            return redirect('/')->withDanger($e->getMessage());
        }
    }
}
