<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // support both ?q= and ?search= from frontend
        if ($request->has('q') || $request->has('search')) {
            $search = $request->get('q', $request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(CustomerRequest $request)
    {
        $data = $request->validated();
        $customer = Customer::create($data);

        // Optionally create a linked user for this customer
        if ($request->has('create_user') && $request->boolean('create_user')) {
            $userData = $request->only(['user_first_name', 'user_last_name', 'user_email', 'user_password']);
            if (!empty($userData['user_email']) && !empty($userData['user_password'])) {
                $u = \App\Models\User::create([
                    'first_name' => $userData['user_first_name'] ?? $data['contact_person'] ?? 'Customer',
                    'last_name' => $userData['user_last_name'] ?? '',
                    'email' => $userData['user_email'],
                    'password' => $userData['user_password'],
                    'status' => 'active',
                ]);

                // assign 'customer' role if exists
                $role = \App\Models\Role::where('name', 'customer')->first();
                if ($role) {
                    $u->roles()->sync([$role->id]);
                }

                // optionally link customer to user if the customer model has user_id attribute
                try {
                    if (in_array('user_id', $customer->getFillable()) || array_key_exists('user_id', $customer->getAttributes())) {
                        $customer->user_id = $u->id;
                        $customer->save();
                    }
                } catch (\Throwable $e) {
                    // ignore if cannot attach
                }
            }
        }

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        // Load only relations whose tables exist to avoid runtime SQL errors when migrations are partial
        $relations = [];
        if (Schema::hasTable('orders')) $relations[] = 'orders';
        if (Schema::hasTable('invoices')) $relations[] = 'invoices';
        if (Schema::hasTable('quotes')) $relations[] = 'quotes';

        if (!empty($relations)) {
            return response()->json($customer->load($relations));
        }

        return response()->json($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }

    public function statistics()
    {
        return response()->json([
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'inactive' => Customer::where('status', 'inactive')->count(),
            'new_this_year' => Customer::whereYear('created_at', date('Y'))->count(),
        ]);
    }
}
