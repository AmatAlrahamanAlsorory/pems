<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\Person;
use App\Models\Location;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $filters = $request->only(['project_id', 'date_from', 'date_to', 'category_id', 'status', 'min_amount', 'max_amount']);
        
        if (empty($query) && empty(array_filter($filters))) {
            return response()->json([]);
        }
        
        $results = [];
        
        if ($type === 'all' || $type === 'projects') {
            $results['projects'] = $this->searchProjects($query, $filters);
        }
        if ($type === 'all' || $type === 'expenses') {
            $results['expenses'] = $this->searchExpenses($query, $filters);
        }
        if ($type === 'all' || $type === 'custodies') {
            $results['custodies'] = $this->searchCustodies($query, $filters);
        }
        if ($type === 'all' || $type === 'people') {
            $results['people'] = $this->searchPeople($query, $filters);
        }
        if ($type === 'all' || $type === 'locations') {
            $results['locations'] = $this->searchLocations($query, $filters);
        }
        
        return response()->json($results);
    }
    
    public function advanced(Request $request)
    {
        $query = $request->get('q', '');
        $filters = $request->only(['type', 'project_id', 'date_from', 'date_to', 'category_id', 'status', 'min_amount', 'max_amount']);
        
        $results = collect();
        
        // بحت متقدم في المصروفات
        if (!isset($filters['type']) || $filters['type'] === 'expenses') {
            $expenses = $this->advancedExpenseSearch($query, $filters);
            $results = $results->merge($expenses);
        }
        
        // بحث متقدم في العهد
        if (!isset($filters['type']) || $filters['type'] === 'custodies') {
            $custodies = $this->advancedCustodySearch($query, $filters);
            $results = $results->merge($custodies);
        }
        
        return view('search.results', compact('results', 'query', 'filters'));
    }
    
    private function searchProjects($query, $filters = [])
    {
        $q = Project::query();
        
        if ($query) {
            $q->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }
        
        if (isset($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        
        return $q->limit(10)->get()->map(function($project) {
            return [
                'type' => 'project',
                'id' => $project->id,
                'title' => $project->name,
                'description' => $project->description,
                'url' => route('projects.show', $project),
                'amount' => $project->total_budget,
                'status' => $project->status
            ];
        });
    }
    
    private function searchExpenses($query, $filters = [])
    {
        $q = Expense::with(['project', 'category', 'item']);
        
        if ($query) {
            $q->where(function($q) use ($query) {
                $q->where('description', 'LIKE', "%{$query}%")
                  ->orWhere('vendor_name', 'LIKE', "%{$query}%")
                  ->orWhere('invoice_number', 'LIKE', "%{$query}%");
            });
        }
        
        if (isset($filters['project_id'])) {
            $q->where('project_id', $filters['project_id']);
        }
        
        if (isset($filters['category_id'])) {
            $q->where('expense_category_id', $filters['category_id']);
        }
        
        if (isset($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        
        if (isset($filters['date_from'])) {
            $q->whereDate('expense_date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $q->whereDate('expense_date', '<=', $filters['date_to']);
        }
        
        if (isset($filters['min_amount'])) {
            $q->where('amount', '>=', $filters['min_amount']);
        }
        
        if (isset($filters['max_amount'])) {
            $q->where('amount', '<=', $filters['max_amount']);
        }
        
        return $q->limit(20)->get()->map(function($expense) {
            return [
                'type' => 'expense',
                'id' => $expense->id,
                'title' => $expense->description,
                'description' => "مشروع: {$expense->project->name} - فئة: {$expense->category->name}",
                'url' => route('expenses.show', $expense),
                'amount' => $expense->amount,
                'date' => $expense->expense_date,
                'status' => $expense->status
            ];
        });
    }
    
    private function advancedExpenseSearch($query, $filters)
    {
        return $this->searchExpenses($query, $filters);
    }
    
    private function searchCustodies($query, $filters = [])
    {
        $q = Custody::with(['project', 'requestedBy']);
        
        if ($query) {
            $q->where(function($q) use ($query) {
                $q->where('purpose', 'LIKE', "%{$query}%")
                  ->orWhere('custody_number', 'LIKE', "%{$query}%")
                  ->orWhereHas('requestedBy', function($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%");
                  });
            });
        }
        
        if (isset($filters['project_id'])) {
            $q->where('project_id', $filters['project_id']);
        }
        
        if (isset($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        
        if (isset($filters['date_from'])) {
            $q->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $q->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $q->limit(10)->get()->map(function($custody) {
            return [
                'type' => 'custody',
                'id' => $custody->id,
                'title' => $custody->custody_number . ' - ' . $custody->purpose,
                'description' => "مستلم: {$custody->requestedBy->name} - مشروع: {$custody->project->name}",
                'url' => route('custodies.show', $custody),
                'amount' => $custody->amount,
                'date' => $custody->created_at,
                'status' => $custody->status
            ];
        });
    }
    
    private function advancedCustodySearch($query, $filters)
    {
        return $this->searchCustodies($query, $filters);
    }
    
    private function searchPeople($query, $filters = [])
    {
        $q = Person::query();
        
        if ($query) {
            $q->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('id_number', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            });
        }
        
        return $q->limit(10)->get()->map(function($person) {
            return [
                'type' => 'person',
                'id' => $person->id,
                'title' => $person->name,
                'description' => "نوع: {$person->type} - هاتف: {$person->phone}",
                'url' => '#',
                'amount' => null
            ];
        });
    }
    
    private function searchLocations($query, $filters = [])
    {
        $q = Location::with('project');
        
        if ($query) {
            $q->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('city', 'LIKE', "%{$query}%")
                  ->orWhere('address', 'LIKE', "%{$query}%");
            });
        }
        
        if (isset($filters['project_id'])) {
            $q->where('project_id', $filters['project_id']);
        }
        
        return $q->limit(10)->get()->map(function($location) {
            return [
                'type' => 'location',
                'id' => $location->id,
                'title' => $location->name,
                'description' => "مشروع: {$location->project->name} - {$location->city}",
                'url' => '#',
                'amount' => null
            ];
        });
    }
}