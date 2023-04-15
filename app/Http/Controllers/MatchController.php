<?php

namespace App\Http\Controllers;

use App\Models\CricketMatch;
use App\Models\Team;
use App\Models\Venue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class MatchController extends Controller
{
    public function ShowMatchList(Request $request)
    {
        try {
            $currentDate = date('Y-m-d H:i:s', strtotime('+6 hours'));
            $matches = CricketMatch::where('time', '<', $currentDate)->where('status', '=', 'upcoming')
                ->get();
            foreach ($matches as $match) {
                $match->status = 'ongoing';
                $match->save();
            }
            if ($request->ajax()) {
                $match = CricketMatch::with(['teamA', 'teamB']);
                return DataTables::of($match)
                    ->addColumn('team_a_name', function ($match) {
                        return $match->teamA->name;
                    })
                    ->addColumn('team_b_name', function ($match) {
                        return $match->teamB->name;
                    })
                    ->rawColumns(['team_a_name', 'team_b_name'])
                    ->make(true);
            }
            $matches = CricketMatch::count();
            return view('pages.matches.matchList')->with('matches', $matches);
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }

    public function ShowAddMatchForm()
    {
        try {
            $teams = Team::all();
            $venues = Venue::all();
            $formats = ['ODI', 'T20', 'T10'];
            return view('pages.matches.addMatch', [
                'venues' => $venues,
                'teams' => $teams,
                'formats' => $formats,
            ]);
        } catch (Exception $error) {
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
    public function AddMatch(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'team_a_id' => 'required',
                'team_b_id' => 'required',
                'venue' => 'required',
                'time' => 'required',
                'format' => 'required',
            ]);
            $format = $request->format;
            $over = 0;
            if ($format == 'ODI') {
                $over = 50;
            } else if ($format == 'T20') {
                $over = 20;
            } else if ($format == 'T10') {
                $over = 10;
            }
            $match = new CricketMatch();
            $match->team_a_id = $request->team_a_id;
            $match->team_b_id = $request->team_b_id;
            $match->venue = $request->venue;
            $match->time = $request->time;
            $match->format = $request->format;
            $match->over = $over;
            $match->status = 'upcoming';
            $match->save();
            DB::commit();
            return redirect('matches')->withSuccess('match added successfully!');
        }
        catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        }
        catch (Exception $error) {
            DB::rollBack();
            return redirect('dashboard')->withDanger($error->getMessage());
        }
    }
}
