<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\Venue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function ShowTeamList(Request $request)
    {
        try {
            if ($request->ajax()) {
                $teams = Team::with('homeVenue')->get();
                return DataTables::of($teams)
                    ->addColumn('home_venue_name', function ($team) {
                        return $team->homeVenue->name;
                    })
                    ->addColumn('actions', function ($row) {
                        return "<a href='" . route('get.team-players', $row->id) . "' class='btn btn-sm btn-primary px-2 mr-2'><i style='font-size: 12px' class='me-1 fas fa-users'></i> Players</a>
                            <a href='" . route('get.team-update', $row->id) . "' class='btn btn-sm btn-success px-2 mr-2'><i style='font-size: 12px' class='me-1 fas fa-wrench'></i> Update</a>
                            <form action='" . route('team.delete', $row->id) . "' method='POST' class='d-inline-block'>
                                " . csrf_field() . "
                                " . method_field('DELETE') . "
                                <button type='submit' class='btn btn-sm btn-danger px-2' onclick='return confirm(\"Are you sure you want to delete this team?\")'> <i style='font-size: 12px' class='me-1 fas fa-trash'></i> Delete</button>
                            </form>";
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            $teams = Team::count();
            return view('pages.teams.teamList')->with('teams', $teams);
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function ShowAddTeamForm()
    {
        try {
            $venues = Venue::all();
            return view('pages.teams.addTeam', ['venues' => $venues]);
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function AddTeam(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'head_coach' => 'required',
                'home_venue_id' => 'required|numeric'
            ]);
            $venue = new Team();
            $venue->name = $request->name;
            $venue->head_coach = $request->head_coach;
            $venue->home_venue_id = $request->home_venue_id;
            $venue->save();
            return redirect('teams')->withSuccess('team added successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function UpdateTeamForm($id)
    {
        try {
            $team = Team::find($id);
            $homeVenue = $team->home_venue_id;
            $venues = Venue::all();
            if (!$team) {
                return redirect('teams')->withDanger('No team found for update!');
            }
            return view('pages.teams.updateTeam', ['team' => $team, 'venues' => $venues, 'homeVenue' => $homeVenue]);
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function UpdateTeam(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required',
                'head_coach' => 'required',
                'home_venue_id' => 'required|numeric'
            ]);
            $check = Team::find($request->id);
            if (!$check) {
                return redirect()->back()->withError('No team found for update!');
            } else {
                $team =  Team::find($request->id);
                $team->name = $request->name;
                $team->head_coach = $request->head_coach;
                $team->home_venue_id = $request->home_venue_id;
                $team->save();
                DB::commit();
                return redirect('teams')->withSuccess('teams update successfully!');
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $error) {
            DB::rollBack();
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function DeleteTeam($id)
    {
        try {
            $team = Team::find($id);
            if ($team) {
                $team->delete();
                return redirect()->route('teams')->with('success', 'Team id ' . $id . ' deleted successfully!');
            } else {
                return redirect()->route('teams')->with('success', 'Team record not found!');
            }
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function getTeamAllPlayersForm($id, Request $request)
    {
        try {
            $team = Team::find($id);
            if (!$team) {
                return redirect('teams')->withDanger('team id ' . $id . ' not found');
            } else {
                $players = $team->teamPlayers;
                $players->map(function ($player) {
                    $player->team_name = $player->team->name;
                    return $player;
                });
                if ($request->ajax()) {
                    return DataTables::of($players)
                        ->addColumn('actions', function ($row) {
                            return "<a href='" . route('get.player-update', $row->id) . "' class='btn btn-sm btn-success px-2 mr-2'><i style='font-size: 12px' class='me-1 fas fa-wrench'></i> Update</a>";
                        })
                        ->rawColumns(['actions'])
                        ->make(true);
                }
                return view(
                    'pages.teams.teamPlayers.teamPlayerList',
                    ['team' => $team, 'players' => $players]
                );
            }
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
}
