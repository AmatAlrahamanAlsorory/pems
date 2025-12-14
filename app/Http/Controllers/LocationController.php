<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Project;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('project')->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('locations.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'budget_allocated' => 'required|numeric|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->all();
        
        // إنشاء رابط الخريطة إذا توفرت الإحداثيات
        if ($request->latitude && $request->longitude) {
            $data['map_url'] = "https://www.google.com/maps?q={$request->latitude},{$request->longitude}";
        }

        Location::create($data);
        return redirect()->route('locations.index')->with('success', 'تم إنشاء الموقع بنجاح');
    }

    public function show(Location $location)
    {
        $location->load(['project', 'custodies', 'expenses']);
        return view('locations.show', compact('location'));
    }
    
    public function map()
    {
        $locations = Location::with('project')->get();
        return view('locations.map', compact('locations'));
    }
    
    public function updateGPS(Request $request, Location $location)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);
        
        $location->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'map_url' => "https://www.google.com/maps?q={$request->latitude},{$request->longitude}"
        ]);
        
        return response()->json(['success' => true, 'message' => 'تم تحديث الموقع بنجاح']);
    }
}