<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\RndQuote;
use App\Models\RndDocument;
use Illuminate\Http\Request;
use App\Models\QaQuote;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderItem;
use DB;

class RndQuoteController extends Controller
{
    public function index()
    {
        $rndDepartments = RndQuote::with(['quote.customer', 'documents'])->latest()->get();
        
        $stats = [
            'total' => RndQuote::count(),
            'pending' => RndQuote::where('status', 'pending')->count(),
            'in_review' => RndQuote::where('status', 'in_review')->count(),
            'approved' => RndQuote::where('status', 'approved')->count(),
            'rejected' => RndQuote::where('status', 'rejected')->count(),
            'with_documents' => RndQuote::has('documents')->count(),
        ];

        return view('rnd.index', compact('rndDepartments', 'stats'));
    }

    public function show($id)
    {
        $rnd = RndQuote::with(['quote.customer', 'quote.products', 'documents'])->findOrFail($id);
        return view('rnd.show', compact('rnd'));
    }

    public function uploadDocuments(Request $request, $id)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120'
        ]);

        $rnd = RndQuote::findOrFail($id);


        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('rnd_documents', $fileName, 'public');

                RndDocument::create([
                    'rnd_quotes_id' => $rnd->id,
                    'document_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id()
                ]);
            }
        }

        $rnd->update(['status' => 'in_review']);
        notify()
    ->title(__('notifications.titles.rnd_documents'))
    ->message(__('notifications.rnd.documents_uploaded', ['number' => $rnd->quote->quotation_number]))
    ->sendToRole(['Administrator','Manager','R&D User']);

        return redirect()->back()->with('success', 'Documents uploaded successfully');
    }

    public function deleteDocument($id)
    {
        $document = RndDocument::findOrFail($id);
        $rndQuote = $document->rndQuote;

        // Check if R&D is not approved (only allow deletion for pending/in_review status)
        if ($rndQuote->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot delete documents after R&D is approved.');
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete the document record
        $document->delete();

        // If no documents left, update status back to pending
        if ($rndQuote->documents()->count() === 0) {
            $rndQuote->update(['status' => 'pending']);
        }

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
public function approve(Request $request, $id)
{
    $request->validate([
        'rnd_notes' => 'nullable|string'
    ]);

    DB::beginTransaction();

    try {
        $rnd = RndQuote::findOrFail($id);
        
        // Update RND quote status
        $rnd->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rnd_notes' => $request->rnd_notes
        ]);

      notify()
    ->title(__('notifications.titles.rnd_approved'))
    ->message(__('notifications.rnd.approved', ['number' => $rnd->quote->quotation_number]))
    ->sendToRole(['Administrator','Manager','Sales User','Quality Control']);


       $rnd->quote->update(['status' => 'rnd_approved']);

        // Commit the transaction
        DB::commit();

        return redirect()->route('rnd.show', $rnd->id)->with('success', 'R&D approved successfully');

    } catch (\Exception $e) {
        // Rollback the transaction in case of error
        DB::rollBack();
        
        // Log the error for debugging
        \Log::error('R&D Approval Error: ' . $e->getMessage(), [
            'rnd_id' => $id,
            'exception' => $e
        ]);

        return redirect()->back()->with('error', 'Failed to approve R&D. Please try again.');
    }
}

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rnd_notes' => 'required|string'
        ]);

        $rnd = RndQuote::findOrFail($id);
        notify()
    ->title(__('notifications.titles.rnd_rejected'))
    ->message(__('notifications.rnd.rejected', ['number' => $rnd->quote->quotation_number]))
    ->sendToRole(['Administrator','Manager','Sales User']);
        $rnd->update([
            'status' => 'rejected',
            'rnd_notes' => $request->rnd_notes
        ]);

        $rnd->quote->update(['status' => 'sent_to_rnd']);

        return redirect()->back()->with('success', 'R&D rejected');
    }

}