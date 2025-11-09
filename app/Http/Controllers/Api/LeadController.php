<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $q = Lead::query();
        if ($search = $request->get('q')) {
            $q->where('company_name', 'like', "%{$search}%")->orWhere('contact_person', 'like', "%{$search}%");
        }
        return response()->json($q->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:new,qualified,contacted,converted,closed',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        $lead = Lead::create($data);
        return response()->json($lead, 201);
    }

    public function show(Lead $lead)
    {
        return response()->json($lead);
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:new,qualified,contacted,converted,closed',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        $lead->update($data);
        return response()->json($lead);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
