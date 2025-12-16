<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Project;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::canManageLocations(auth()->user())) {
            abort(403, 'غير مصرح لك بعرض المواقع');
        }
        
        $locations = Location::with(['project', 'expenses'])->get();
        $projects = Project::where('status', 'active')->get();
        return view('locations.index', compact('locations', 'projects'));
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
        if (!PermissionHelper::canManageLocations(auth()->user())) {
            abort(403, 'غير مصرح لك بعرض خريطة المواقع');
        }
        
        $locations = Location::with(['project', 'expenses'])
            ->get()
            ->map(function($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'address' => $location->address,
                    'status' => $location->status ?? 'active',
                    'project_id' => $location->project_id,
                    'project' => $location->project,
                    'budget' => $location->budget ?? 0,
                    'spent_amount' => $location->expenses->sum('amount'),
                    'expenses_count' => $location->expenses->count(),
                    'last_activity' => $location->expenses->max('expense_date')
                ];
            });
            
        $projects = \App\Models\Project::all();
        
        return view('locations.map', compact('locations', 'projects'));
    }
    
    public function edit(Location $location)
    {
        $projects = Project::where('status', 'active')->get();
        return view('locations.edit', compact('location', 'projects'));
    }
    
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->all();
        
        if ($request->latitude && $request->longitude) {
            $data['map_url'] = "https://www.google.com/maps?q={$request->latitude},{$request->longitude}";
        }

        $location->update($data);
        return redirect()->route('locations.index')->with('success', 'تم تحديث الموقع بنجاح');
    }
    
    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'تم حذف الموقع بنجاح');
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