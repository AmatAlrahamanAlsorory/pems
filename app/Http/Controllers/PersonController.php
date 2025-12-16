<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::latest()->paginate(20);
        return view('people.index', compact('people'));
    }

    public function create()
    {
        return view('people.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:actor,director,producer,crew,other',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        Person::create($validated);
        return redirect()->route('people.index')->with('success', 'تم إضافة الشخص بنجاح');
    }

    public function show(Person $person)
    {
        $person->load(['expenses.project', 'expenses.category']);
        return view('people.show', compact('person'));
    }

    public function edit(Person $person)
    {
        return view('people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:actor,director,producer,crew,other',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $person->update($validated);
        return redirect()->route('people.show', $person)->with('success', 'تم تحديث بيانات الشخص بنجاح');
    }

    public function destroy(Person $person)
    {
        // فحص إذا كان الشخص مرتبط بمصروفات
        if ($person->expenses()->count() > 0) {
            return back()->withErrors(['delete' => 'لا يمكن حذف هذا الشخص لأنه مرتبط بمصروفات']);
        }

        $person->delete();
        return redirect()->route('people.index')->with('success', 'تم حذف الشخص بنجاح');
    }
}