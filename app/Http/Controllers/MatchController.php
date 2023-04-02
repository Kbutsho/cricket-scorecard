<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MatchController extends Controller
{
    public function ShowMatchList(Request $request)
    {
        // updating status with time
        $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $matches = CricketMatch::where('status', '=', 'upcoming')
            ->where('time', '<', $currentDate)
            ->get();

        foreach ($matches as $match) {
            $match->status = 'ongoing';
            $match->save();
        }

        if ($request->ajax()) {
            $match = CricketMatch::query();
            return DataTables::of($match)
                ->make(true);
        }
        $matches = CricketMatch::count();
        return view('pages.matches.matchList')->with('matches', $matches);
    }
    public function ShowAddMatchForm()
    {
        $teams = Team::all();
        $venues = Venue::all();
        $formats = ['TEST', 'ODI', 'T20'];
        return view('pages.matches.addMatch', [
            'venues' => $venues,
            'teams' => $teams,
            'formats' => $formats
        ]);
    }
    public function AddMatch(Request $request)
    {
        $request->validate([
            'team_a' => 'required',
            'team_b' => 'required',
            'venue' => 'required',
            'time' => 'required',
            'format' => 'required'
        ]);
        $match = new CricketMatch();
        $match->team_a = $request->team_a;
        $match->team_b = $request->team_b;
        $match->venue = $request->venue;
        $match->time = $request->time;
        $match->format = $request->format;
        $match->status = 'upcoming';
        $match->save();
        return redirect('matches')->withSuccess('match added successfully!');
    }
}
