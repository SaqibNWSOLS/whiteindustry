<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(InventoryRequest $request)
    {
        $data = $request->validated();

        // Normalize type aliases to canonical values to avoid wrong storage when clients send labels
        if (!empty($data['type'])) {
            $t = strtolower(trim($data['type']));
            if (in_array($t, ['raw material', 'raw_material', 'raw', 'material'])) {
                $data['type'] = 'raw_material';
            } elseif (in_array($t, ['packaging', 'package', 'pack'])) {
                $data['type'] = 'packaging';
            } elseif (in_array($t, ['final product', 'final_product', 'final', 'finished', 'product'])) {
                $data['type'] = 'final_product';
            } elseif (in_array($t, ['blend', 'blend product', 'blended', 'mix'])) {
                $data['type'] = 'blend';
            }
        }

        // Support initial_stock via transaction: remove from create payload and set current_stock to 0
        $initial = null;
        if (isset($data['initial_stock'])) {
            $initial = $data['initial_stock'];
            unset($data['initial_stock']);
            // ensure the created record starts with zero then transaction will adjust
            $data['current_stock'] = $data['current_stock'] ?? 0;
        }

        // If creating packaging and a composition is provided, compute packaging unit_cost
        // composition expected as array of { inventory_id, percentage } and packaging_volume may be provided
        if (!empty($data['type']) && $data['type'] === 'packaging' && !empty($data['composition']) && is_array($data['composition'])) {
            $packagingVolume = isset($data['packaging_volume']) ? floatval($data['packaging_volume']) : 1.0;
            $packCost = 0.0;
            $totalPercent = array_sum(array_column($data['composition'], 'percentage')) ?: 0;
            // If composition percentages sum approximately to 100, use percentages, otherwise treat provided values as quantities
            $usePercent = $totalPercent > 0;
            foreach ($data['composition'] as $comp) {
                if (!isset($comp['inventory_id'])) continue;
                $inv = Inventory::find($comp['inventory_id']);
                if (!$inv) continue;
                $unitCost = floatval($inv->unit_cost ?? 0);
                if ($usePercent) {
                    $pct = floatval($comp['percentage']) / 100.0;
                    $qty = $packagingVolume * $pct;
                } else {
                    // if no percentages provided, accept 'quantity' field per component
                    $qty = isset($comp['quantity']) ? floatval($comp['quantity']) : 0;
                }
                $packCost += $unitCost * $qty;
            }
            // set unit_cost for packaging per package
            $data['unit_cost'] = round($packCost, 6);
        }

        $inventory = Inventory::create($data);

        $inventory->updateStatus();

        if ($initial && $initial > 0) {
            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'type' => 'in',
                'quantity' => $initial,
                'notes' => 'Initial stock (manufactured)',
                'user_id' => $request->user()?->id,
            ]);
            // reload with new stock
            $inventory = $inventory->fresh();
        }

        return response()->json(['success' => true, 'data' => $inventory], 201);
    }

    /**
     * Calculate cost breakdown for a manufactured/assembled product.
     * Expected payload:
     * {
     *   materials: [{ inventory_id: int, percentage: numeric }, ...],
     *   packaging_id: int|null,
     *   batch_size: numeric (units),
     *   manufacture_percent: numeric (defaults 30),
     *   risk_percent: numeric (defaults 5),
     *   profit_percent: numeric (defaults 30),
     *   tax_percent: numeric (defaults 19)
     * }
     */
    public function calculateCost(Request $request)
    {
        $payload = $request->validate([
            'materials' => 'required|array|min:1',
            'materials.*.inventory_id' => 'required|integer|exists:inventory,id',
            'materials.*.percentage' => 'required|numeric|min:0.0001|max:100',
            'packaging_id' => 'nullable|integer|exists:inventory,id',
            'packaging_composition' => 'nullable|array',
            'packaging_volume' => 'nullable|numeric|min:0',
            'batch_size' => 'nullable|numeric|min:0.0001',
            'manufacture_percent' => 'nullable|numeric|min:0',
            'risk_percent' => 'nullable|numeric|min:0',
            'profit_percent' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'commission_percent' => 'nullable|numeric|min:0',
        ]);

        $materials = $payload['materials'];
        $totalPercent = array_sum(array_column($materials, 'percentage'));
        // allow slight floating point tolerance
        if (abs($totalPercent - 100) > 0.5) {
            return response()->json(['success' => false, 'message' => 'Total materials percentage must sum to ~100% (tolerance 0.5%)', 'total' => $totalPercent], 422);
        }

    $batch = $payload['batch_size'] ?? 1;
    $packagingVolume = $payload['packaging_volume'] ?? null; // per final unit
        $manufacturePercent = $payload['manufacture_percent'] ?? 30;
        $riskPercent = $payload['risk_percent'] ?? 5;
        $profitPercent = $payload['profit_percent'] ?? 30;
        $taxPercent = $payload['tax_percent'] ?? 19;

        // Determine blend volume. If packaging_volume provided, blend volume = batch_size * packaging_volume
        // else blend volume defaults to batch (1 unit volume per unit produced)
        $blendVolume = $batch * ($packagingVolume !== null ? floatval($packagingVolume) : 1.0);

        // Calculate raw materials required and costs based on blend volume
        $rawTotal = 0.0;
        $rawDetails = [];
        foreach ($materials as $m) {
            $inv = Inventory::find($m['inventory_id']);
            $pct = floatval($m['percentage']) / 100.0;
            // quantity of this material required for the blend (in same unit as packaging_volume)
            $quantity = $blendVolume * $pct;
            $unitCost = floatval($inv->unit_cost ?? 0); // assumed cost per unit of material (per volume or mass)
            $contribution = $unitCost * $quantity; // total contribution for batch
            $rawTotal += $contribution;
            $rawDetails[] = [
                'id' => $inv->id,
                'material_code' => $inv->material_code,
                'name' => $inv->name,
                'percentage' => floatval($m['percentage']),
                'unit_cost' => $unitCost,
                'quantity' => round($quantity, 6),
                'contribution' => round($contribution, 6),
            ];
        }

        // packaging cost total: packaging unit_cost is treated as per-unit packaging (per final packaged unit)
        $packTotal = 0.0;
        $packaging = null;
        // If a dynamic packaging composition is provided (create-on-the-fly packaging), use it to compute packaging cost
        if (!empty($payload['packaging_composition']) && is_array($payload['packaging_composition'])) {
            $packagingVolume = $payload['packaging_volume'] ?? 1.0;
            $packCost = 0.0;
            $totalPercent = array_sum(array_column($payload['packaging_composition'], 'percentage')) ?: 0;
            $usePercent = $totalPercent > 0;
            foreach ($payload['packaging_composition'] as $comp) {
                if (empty($comp['inventory_id'])) continue;
                $inv = Inventory::find($comp['inventory_id']);
                if (!$inv) continue;
                $unitCost = floatval($inv->unit_cost ?? 0);
                if ($usePercent) {
                    $pct = floatval($comp['percentage']) / 100.0;
                    $qty = $packagingVolume * $pct;
                } else {
                    $qty = isset($comp['quantity']) ? floatval($comp['quantity']) : 0;
                }
                $packCost += $unitCost * $qty;
            }
            $packTotal = round($packCost * $batch, 6);
        } elseif (!empty($payload['packaging_id'])) {
            $packaging = Inventory::find($payload['packaging_id']);
            if ($packaging) $packTotal = floatval($packaging->unit_cost ?? 0) * $batch;
        }

    $base = $rawTotal + $packTotal;
        $manufactureCost = $base * ($manufacturePercent / 100.0);
        $riskCost = $base * ($riskPercent / 100.0);
        $subtotal = $base + $manufactureCost + $riskCost;
        $profit = $subtotal * ($profitPercent / 100.0);
        $totalWithoutTax = $subtotal + $profit;

        // commission (optional) — applied as percent on the pre-tax total
        $commissionPercent = $payload['commission_percent'] ?? 0;
        $commission = 0.0;
        if ($commissionPercent && $commissionPercent > 0) {
            $commission = $totalWithoutTax * ($commissionPercent / 100.0);
        }

        $totalWithCommission = $totalWithoutTax + $commission;
        $tax = $totalWithCommission * ($taxPercent / 100.0);
        $totalWithTax = $totalWithCommission + $tax;

        $result = [
            'materials' => $rawDetails,
            'blend_volume' => round($blendVolume, 6),
            'batch_size' => $batch,
            'raw_total' => round($rawTotal, 6),
            'pack_total' => round($packTotal, 6),
            'manufacture_cost' => round($manufactureCost, 6),
            'risk_cost' => round($riskCost, 6),
            'profit' => round($profit, 6),
            'tax' => round($tax, 6),
            'commission' => round($commission, 6),
            'commission_percent' => round(floatval($commissionPercent ?? 0), 2),
            'total_without_tax' => round($totalWithoutTax, 6),
            'total_with_commission' => round($totalWithCommission, 6),
            'total_with_tax' => round($totalWithTax, 6),
            'unit_cost_without_tax' => round($totalWithoutTax / max(1, $batch), 6),
            'unit_cost_with_commission' => round($totalWithCommission / max(1, $batch), 6),
            'unit_cost_with_tax' => round($totalWithTax / max(1, $batch), 6),
        ];

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function show(Inventory $inventory)
    {
        return response()->json($inventory->load('transactions'));
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $inventory->update($request->validated());
        $inventory->updateStatus();
        
        return response()->json($inventory);
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return response()->json(null, 204);
    }

    public function adjustStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string',
        ]);

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($inventory->fresh());
    }

    public function lowStock()
    {
        return response()->json(
            Inventory::where('status', 'low_stock')->get()
        );
    }

    public function statistics()
    {
        return response()->json([
            'total_items' => Inventory::count(),
            'low_stock_alerts' => Inventory::where('status', 'low_stock')->count(),
            'out_of_stock' => Inventory::where('status', 'out_of_stock')->count(),
            'total_value' => Inventory::selectRaw('SUM(current_stock * unit_cost) as value')->value('value'),
        ]);
    }
}