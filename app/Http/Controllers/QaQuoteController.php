<?php

namespace App\Http\Controllers;

use App\Models\QaQuote;
use App\Models\QaDocument;
use App\Models\RndQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\InventoryTransaction;
use DB;

class QaQuoteController extends Controller
{
    public function index()
    {
        $qaDepartments = QaQuote::with(['order.customer', 'rndQuote', 'documents'])->latest()->get();
        
        $stats = [
            'total' => QaQuote::count(),
            'pending' => QaQuote::where('status', 'pending')->count(),
            'in_review' => QaQuote::where('status', 'in_review')->count(),
            'approved' => QaQuote::where('status', 'approved')->count(),
            'rejected' => QaQuote::where('status', 'rejected')->count(),
            'with_documents' => QaQuote::has('documents')->count(),
        ];

        return view('qa.index', compact('qaDepartments', 'stats'));
    }

    public function production($id){

         $production = Production::with([
            'inventoryTransactions.product',
            'inventoryTransactions.productionItem.orderProduct',
            'inventoryTransactions.createdBy'
        ])->findOrFail($id);
          $pendingTransactions = $production->inventoryTransactions()
        ->where('inventory_transactions.status', 'pending')
        ->get();
        

        return view('qa.inventory',compact('production','pendingTransactions'));

    }

    public function show($id)
    {
        $qa = QaQuote::with(['order.customer', 'order.products', 'rndQuote.documents', 'documents'])->findOrFail($id);
        return view('qa.show', compact('qa'));
    }

    public function uploadDocuments(Request $request, $id)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120'
        ]);

        $qa = QaQuote::findOrFail($id);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('qa_documents', $fileName, 'public');

                QaDocument::create([
                    'qa_quotes_id' => $qa->id,
                    'document_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id()
                ]);
            }
        }

        $qa->update(['status' => 'in_review']);

        return redirect()->back()->with('success', 'Documents uploaded successfully');
    }

    public function deleteDocument($id)
    {
        $document = QaDocument::findOrFail($id);
        $qaQuote = $document->qaQuote;

        // Check if QA is not approved (only allow deletion for pending/in_review status)
        if ($qaQuote->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot delete documents after QA is approved.');
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete the document record
        $document->delete();

        // If no documents left, update status back to pending
        if ($qaQuote->documents()->count() === 0) {
            $qaQuote->update(['status' => 'pending']);
        }

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'qa_notes' => 'nullable|string'
        ]);

        $qa = QaQuote::findOrFail($id);
        $order=Order::where('id',$qa->orders_id)->first();
        $qa->update([
            'status' => 'approved',
            'approved_at' => now(),
            'qa_notes' => $request->qa_notes
        ]);

        $production = Production::create([
            'production_number' => 'PROD-' . Str::random(8),
            'order_id' => $order->id,
            'start_date' => $order->order_date,
            'production_notes' => $order->order_notes,
            'status' => 'pending'
        ]);

        // Create production items
        foreach ($order->products as $item) {
            ProductionItem::create([
                'production_id' => $production->id,
                'order_product_id' => $item->id,
                'quantity_planned' => $item->quantity,
                'quantity_produced' => 0,
                'status' => 'pending'
            ]);
        }
        $qa->update(['production_id'=>$production->id]);

        $order->update(['status' => 'production']);



        return redirect()->route('qa.show', $qa->id)->with('success', 'QA approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'qa_notes' => 'required|string'
        ]);

        $qa = QaQuote::findOrFail($id);
        $qa->update([
            'status' => 'rejected',
            'qa_notes' => $request->qa_notes
        ]);

        return redirect()->back()->with('success', 'QA rejected');
    }

    public function approveInventory(InventoryTransaction $transaction)
{
    try {
        DB::transaction(function() use ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // If it's a pending transaction, actually update the stock
                $product = $transaction->product;
                if ($product) {
                    $product->current_stock += $transaction->quantity_change;
                    $product->save();
                }
        });

        return response()->json([
            'success' => true,
            'message' => __('production.inventory_history.messages.transaction_approved')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function rejectInventory(InventoryTransaction $transaction, Request $request)
{
    $request->validate([
        'reject_reason' => 'required|string|max:500'
    ]);

    try {
        $transaction->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reject_reason' => $request->reject_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => __('production.inventory_history.messages.transaction_rejected')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function approveAll(Production $production)
{
    try {
        DB::transaction(function() use ($production) {
            $pendingTransactions = $production->inventoryTransactions()
                ->where('status', 'pending')
                ->get();

            foreach ($pendingTransactions as $transaction) {
                $transaction->update([
                    'status' => 'completed',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                // Update stock for each transaction
                $product = $transaction->product;
                if ($product) {
                    $product->current_stock += $transaction->quantity_change;
                    $product->save();
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('production.inventory_history.messages.all_transactions_approved')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function rejectAll(Production $production, Request $request)
{
    $request->validate([
        'reject_reason' => 'required|string|max:500'
    ]);

    try {
        $production->inventoryTransactions()
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'reject_reason' => $request->reject_reason
            ]);

        return response()->json([
            'success' => true,
            'message' => __('production.inventory_history.messages.all_transactions_rejected')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}