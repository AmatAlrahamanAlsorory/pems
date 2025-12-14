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
            'type' => 'required|in:actor,technician,crew',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:20',
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
            'type' => 'required|in:actor,technician,crew',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $person->update($validated);
        return redirect()->route('people.index')->with('success', 'تم تحديث بيانات الشخص بنجاح');
    }
}