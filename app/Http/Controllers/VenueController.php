<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables as DataTables;

class VenueController extends Controller
{
    public function ShowVenueList(Request $request)
    {
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
    }
    public function ShowAddVenueForm()
    {
        return view('pages.venues.addVenue');
    }
    public function AddVenue(Request $request)
    {
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
        return redirect('venues')->withSuccess('venue added successfully!');
    }
    public function UpdateVenueForm($id){
        $venue = Venue::find($id);
        if (!$venue) {
            return redirect('venues')->withDanger('No venue found for update!');
        }
        return view('pages.venues.updateVenue', ['venue' => $venue]);
    }
    public function UpdateVenue(Request $request){
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'capacity' => 'required|numeric'
        ]);
        $check = Venue::find($request->id);
        if (!$check) {
            return redirect()->back()->withError('No venue found for update!');
        }else{
            $venue =  Venue::find($request->id);
            $venue->name = $request->name;
            $venue->location = $request->location;
            $venue->capacity = $request->capacity;
            $venue->save();
            return redirect('venues')->withSuccess('venue update successfully!');
        }
    }
    public function DeleteVenue($id)
    {
        $log = Venue::find($id);
        if ($log) {
            $log->delete();
            return redirect()->route('venues')->with('success', 'Venue id ' . $id . ' deleted successfully!');
        } else {
            return redirect()->route('venues')->with('message', 'Venue record not found!');
        }
    }
}
