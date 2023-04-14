<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PlayerController extends Controller
{
    public function ShowPlayerList(Request $request)
    {
        if ($request->ajax()) {
            $players = Player::with('team')->get();

            return DataTables::of($players)
                ->addColumn('team_name', function ($team) {
                    return $team->team->name;
                })
                ->addColumn('actions', function ($row) {
                    return "<a href='" . route('get.player-update', $row->id) . "' class='btn btn-sm btn-success px-2 mr-2'><i style='font-size: 12px' class='me-1 fas fa-wrench'></i> Update</a>
                        <form action='" . route('player.delete', $row->id) . "' method='POST' class='d-inline-block'>
                            " . csrf_field() . "
                            " . method_field('DELETE') . "
                            <button type='submit' class='btn btn-sm btn-danger px-2' onclick='return confirm(\"Are you sure you want to delete this player?\")'> <i style='font-size: 12px' class='me-1 fas fa-trash'></i>Delete </button>
                     </form>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        $players = Player::count();
        return view('pages.players.playerList')->with('players', $players);
    }

    public function ShowAddPlayerForm()
    {
        $teams = Team::all();
        $roles = ['Batting AllRounder', 'Bowling AllRounder', 'WK Batsman', 'Batsman', 'Bowler'];
        $battingStyle = ['Right handed', 'Left handed'];
        $bowlingStyle = ['Right arm pace', 'Left arm pace', 'Left arm spin', 'Right arm spin', 'N/A'];
        return view('pages.players.addPlayer', ['teams' => $teams, 'battingStyle' => $battingStyle, 'bowlingStyle' => $bowlingStyle, 'roles' => $roles]);
    }

    public function AddPlayer(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // 'image' => 'required',
            'team_id' => 'required',
            'role' => 'required',
            'batting_style' => 'required',
            'bowling_style' => 'required',
            'born' => 'required',
            'biography' => 'required'
        ]);
        $player = new Player();
        $player->name = $request->name;
        $player->status = 1;
        $player->team_id = $request->team_id;
        $player->role = $request->role;
        $player->batting_style = $request->batting_style;
        $player->bowling_style = $request->bowling_style;
        $player->born = $request->born;
        $player->biography = $request->biography;
        $player->save();
        return redirect('players')->withSuccess('player added successfully!');
    }
    public function UpdatePlayerForm($id)
    {
        $player = Player::find($id);
        $teams = Team::all();
        $team = $player->team;
        $batting = $player->batting_style;
        $bowling = $player->bowling_style;
        $role = $player->role;

        $roles = ['Batting AllRounder', 'Bowling AllRounder', 'WK Batsman', 'Batsman', 'Bowler'];
        $battingStyle = ['Right handed', 'Left handed'];
        $bowlingStyle = ['Right arm pace', 'Left arm pace', 'Left arm spin', 'Right arm spin', 'N/A'];
        $status =
            [
                'Active' => 1,
                'Inactive' => 0,
                'Injured' => 0,
                'Retired' => 0
            ];

        if (!$player) {
            return redirect('players')->withDanger('No player found for update!');
        }
        return view(
            'pages.players.updatePlayer',
            [
                'status' => $status,
                'team' => $team,
                'teams' => $teams,
                'player' => $player,
                'batting' => $batting,
                'bowling' => $bowling,
                'role' => $role,
                'roles' => $roles,
                'battingStyle' => $battingStyle,
                'bowlingStyle' => $bowlingStyle,
            ]
        );
    }
    public function UpdatePlayer(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'team_id' => 'required',
            'role' => 'required',
            'batting_style' => 'required',
            'bowling_style' => 'required',
            'born' => 'required',
            'biography' => 'required',
            'status' => 'required'
        ]);
        $check = Player::find($request->id);
        if (!$check) {
            return redirect()->back()->withError('No player found for update!');
        } else {
            $player =  Player::find($request->id);
            $player->name = $request->name;
            $player->role = $request->role;
            $player->status = $request->status;
            $player->team_id = $request->team_id;
            $player->batting_style = $request->batting_style;
            $player->bowling_style = $request->bowling_style;
            $player->born = $request->born;
            $player->biography = $request->biography;
            $player->save();
            return redirect('players')->withSuccess('players update successfully!');
        }
    }
    public function DeletePlayer($id)
    {
        $player = Player::find($id);
        if ($player) {
            $player->delete();
            return redirect()->route('players')->with('success', 'player id ' . $id . ' deleted successfully!');
        } else {
            return redirect()->route('players')->with('success', 'player record not found!');
        }
    }
}
