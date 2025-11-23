<?php
namespace App\Http\Controllers;


use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'customer'); // 'customer' or 'lead'
        $search = $request->get('q');
        
        $query = Customer::query();
        
        if ($type === 'lead') {
            $query->lead();
        } else {
            $query->customer();
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $items = $query->paginate(15);
        
        return view('crm.index', [
            'customers' => $items,
            'type' => $type,
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'customer'); // 'customer' or 'lead'
        return view('crm.create', ['type' => $type]);
    }

    public function store(Request $request)
    {
        $type = $request->get('type', 'customer');
       /* 
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'source' => 'nullable|string|in:website,referral,trade_show,cold_call,social_media',
            'status' => 'nullable|string',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);*/

        $validated['type'] = $type;
        
        // Set default status based on type
        if (!isset($validated['status']) || empty($validated['status'])) {
            $validated['status'] = $type === 'lead' ? 'new' : 'active';
        }

        $customer = Customer::create($validated);

        return redirect()->route('customers.index', ['type' => $type])
                       ->with('success', ucfirst($type) . ' created successfully');
    }

    public function edit(Customer $customer)
    {
        return view('crm.edit', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'source' => 'nullable|string|in:website,referral,trade_show,cold_call,social_media',
            'status' => 'nullable|string',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'convert_to_customer' => 'nullable|boolean',
        ]);

        // Handle conversion from lead to customer
        if ($customer->type === 'lead' && $request->boolean('convert_to_customer')) {
            $customer->convertToCustomer();
            return redirect()->route('customers.index', ['type' => 'customer'])
                           ->with('success', 'Lead converted to customer successfully');
        }

        $customer->update($validated);

        return redirect()->route('customers.index', ['type' => $customer->type])
                       ->with('success', ucfirst($customer->type) . ' updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $type = $customer->type;
        $customer->delete();

        return redirect()->route('customers.index', ['type' => $type])
                       ->with('success', ucfirst($type) . ' deleted successfully');
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'customer');
        
        $query = Customer::query();
        if ($type === 'lead') {
            $query->lead();
        } else {
            $query->customer();
        }
        
        $items = $query->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('info', 'No data to export');
        }

        $filename = 'crm-' . $type . '-' . date('Ymd') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv;charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($items, $type) {
            $output = fopen('php://output', 'w');
            
            if ($type === 'lead') {
                fputcsv($output, ['ID', 'Company', 'Contact', 'Email', 'Phone', 'Source', 'Status', 'Value']);
                foreach ($items as $item) {
                    fputcsv($output, [
                        $item->id,
                        $item->company_name,
                        $item->contact_person,
                        $item->email,
                        $item->phone,
                        $item->source,
                        $item->status,
                        $item->estimated_value ?? 0,
                    ]);
                }
            } else {
                fputcsv($output, ['ID', 'Company', 'Contact', 'Email', 'Phone', 'Address', 'City', 'Status']);
                foreach ($items as $item) {
                    fputcsv($output, [
                        $item->id,
                        $item->company_name,
                        $item->contact_person,
                        $item->email,
                        $item->phone,
                        $item->address,
                        $item->city,
                        $item->status,
                    ]);
                }
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}