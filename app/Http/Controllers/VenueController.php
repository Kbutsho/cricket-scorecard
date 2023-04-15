<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables as DataTables;

class VenueController extends Controller
{
    public function ShowVenueList(Request $request)
    {
        try {
            if ($request->ajax()) {
                $venues = Venue::query();
                return DataTables::of($venues)
                    ->addColumn('actions', function ($row) {
                        return "<a href='" . route('get.venue-update', $row->id) . "' class='btn btn-sm btn-success px-2 mr-2'><i style='font-size: 12px' class='me-1 fas fa-wrench'></i> Update</a>
                    <form action='" . route('venue.delete', $row->id) . "' method='POST' class='d-inline-block'>
                        " . csrf_field() . "
                        " . method_field('DELETE') . "
                        <button type='submit' class='btn btn-sm btn-danger px-2' onclick='return confirm(\"Are you sure you want to delete this venue?\")'> <i style='font-size: 12px' class='me-1 fas fa-trash'></i>Delete </button>
                 </form>";
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            $count = Venue::count();
            return view('pages.venues.venueList')->with('venues', $count);
        } catch (Exception $e) {
            return redirect()->route('dashboard')->withDanger($e->getMessage());
        }
    }
    public function ShowAddVenueForm()
    {
        return view('pages.venues.addVenue');
    }
    public function AddVenue(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required',
                'location' => 'required',
                'capacity' => 'required|numeric'
            ]);
            $venue = new Venue();
            $venue->name = $request->name;
            $venue->location = $request->location;
            $venue->capacity = $request->capacity;
            $venue->save();
            DB::commit();
            return redirect('venues')->withSuccess('venue added successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
    public function UpdateVenueForm($id)
    {
        try {
            $venue = Venue::find($id);
            if (!$venue) {
                return redirect('venues')->withDanger('No venue found for update!');
            }
            return view('pages.venues.updateVenue', ['venue' => $venue]);
        } catch (Exception $e) {
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
    public function UpdateVenue(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required',
                'location' => 'required',
                'capacity' => 'required|numeric'
            ]);
            $check = Venue::find($request->id);
            if (!$check) {
                return redirect()->back()->withError('No venue found for update!');
            } else {
                $venue =  Venue::find($request->id);
                $venue->name = $request->name;
                $venue->location = $request->location;
                $venue->capacity = $request->capacity;
                $venue->save();
                DB::commit();
                return redirect('venues')->withSuccess('venue update successfully!');
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
    public function DeleteVenue($id)
    {
        try {
            $log = Venue::find($id);
            if ($log) {
                $log->delete();
                return redirect()->route('venues')->with('success', 'Venue id ' . $id . ' deleted successfully!');
            } else {
                return redirect()->route('venues')->with('message', 'Venue record not found!');
            }
        } catch (Exception $e) {
            return redirect('dashboard')->withDanger($e->getMessage());
        }
    }
}
