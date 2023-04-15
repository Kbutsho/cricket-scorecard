<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Innings;
use App\Models\Squad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LiveMatchController extends Controller
{
    public function ShowAdminLiveMatchList()
    {
        try {
            $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
            CricketMatch::where('time', '<', $currentDate)->where('status', '=', 'upcoming')
                ->update(['status' => 'ongoing']);
            $matches = CricketMatch::with(['teamA', 'teamB'])
                ->where('time', '<', $currentDate)
                ->where('status', '=', 'ongoing')
                ->get();
            return view('pages.matches.live.liveMatchList', [
                'liveMatches' => $matches,
            ]);
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function showSquadForm($id)
    {
        try {
            $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
            // $match = CricketMatch::with(['teamA.teamPlayers', 'teamB.teamPlayers'])
            //     ->where('id', '=', $id)
            //     ->where('time', '<', $currentDate)
            //     ->first();
            $match = CricketMatch::with([
                'teamA.teamPlayers' => function ($query) {
                    $query->where('status', '=', '1');
                }, 'teamB.teamPlayers' => function ($query) {
                    $query->where('status', '=', '1');
                }
            ])
                ->where('id', '=', $id)
                ->where('time', '<', $currentDate)
                ->first();
            $checkInnings = Innings::where('match_id', $id)->get();
            if ($checkInnings && count($checkInnings) > 0) {
                return redirect()->route('get.live.match.score', ['id' => $id]);
            }
            if (!$match) {
                throw new Exception('match id ' . $id . ' not found for squad list!');
            }
            return view(
                'pages.matches.live.liveMatchSquadForm',
                [
                    'match' => $match
                ]
            );
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function saveSquad(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate(
                [
                    'firstBattingTeamId' => 'required',
                    'firstBowlingTeamId' => 'required',
                    'teamA' => 'required|array|size:11',
                    'teamB' => 'required|array|size:11',
                ],
                [
                    'firstBattingTeamId.required' => 'select batting team',
                    'firstBowlingTeamId.required' => 'select bowling team',
                    'teamA.required' => 'select 11 players',
                    'teamB.required' => 'select 11 players',
                    'teamA.size' => 'select 11 players',
                    'teamB.size' => 'select 11 players',
                ]
            );
            // save innings 1 
            $firstInnings = 1;
            $firstBattingTeamId = $request->firstBattingTeamId;
            $firstBowlingTeamId = $request->firstBowlingTeamId;
            $innings1 = new Innings();
            $innings1->match_id = $request->match_id;
            $innings1->battingTeam_id = $firstBattingTeamId;
            $innings1->bowlingTeam_id = $firstBowlingTeamId;
            $innings1->innings = $firstInnings;
            $innings1->status = 1;
            $innings1->save();

            // save innings 2 
            $secondInnings = 2;
            $secondBattingTeamId = $request->firstBowlingTeamId;
            $secondBowlingTeamId = $request->firstBattingTeamId;
            $innings2 = new Innings();
            $innings2->match_id = $request->match_id;
            $innings2->battingTeam_id = $secondBattingTeamId;
            $innings2->bowlingTeam_id = $secondBowlingTeamId;
            $innings2->innings = $secondInnings;
            $innings2->status = 0;
            $innings2->save();

            $player_id = $request->input('player_id');
            $player_name = $request->input('player_name');
            $team_id = $request->input('team_id');
            $team_name = $request->input('team_name');

            foreach ($player_id as $i => $data) {
                $var = new Squad();
                $var->match_id = $request->match_id;
                $var->player_id = $player_id[$i];
                $var->player_name = $player_name[$i];
                $var->team_id = $team_id[$i];
                $var->team_name = $team_name[$i];
                $var->save();
            }
            DB::commit();
            return redirect()->route('get.live.match.score', ['id' => $id])
                ->withSuccess('squad selected successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $error) {
            DB::rollBack();
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
}
