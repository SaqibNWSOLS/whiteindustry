<?php

namespace App\Http\Controllers;

use App\Models\QaQuote;
use App\Models\QaDocument;
use App\Models\RndQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        $qa->update([
            'status' => 'approved',
            'approved_at' => now(),
            'qa_notes' => $request->qa_notes
        ]);


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
}