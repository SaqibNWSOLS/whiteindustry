<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['uploader']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sop,certificate,contract,report,invoice,other',
            'file' => 'required|file|max:10240', // 10MB max
            'version' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
        ]);

            $file = $request->file('file');

            if (!$file || !$file->isValid()) {
                $err = $file ? $file->getError() : 'no_file';
                return response()->json(['message' => 'Uploaded file is missing or invalid', 'error' => $err], 422);
            }

            // Capture metadata before moving (some methods may change internals)
            $originalName = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $size = $file->getSize();

            // Build a safe, unique filename
            $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '_' . $safeBase . ($ext ? '.' . $ext : '');

            // Try to move into a dedicated public uploads folder (avoid naming that collides with top-level routes like '/documents')
            $destDir = public_path('uploaded_documents');
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0755, true);
            }

            $relativePath = null;
            $movedToPublic = false;

            // First attempt: move to public/uploaded_documents
            try {
                $file->move($destDir, $filename);
                $relativePath = 'uploaded_documents/' . $filename;
                $movedToPublic = true;
            } catch (\Exception $e) {
                Log::debug('Document move to public failed', ['error' => $e->getMessage()]);
                // fallback: try storage disk (public disk -> storage/app/public/documents)
                try {
                    $stored = $file->storeAs('documents', $filename, 'public');
                    if ($stored) {
                        $relativePath = $stored; // 'documents/filename'
                        $movedToPublic = false;
                    }
                } catch (\Exception $e2) {
                    Log::error('Document fallback store failed', ['error' => $e2->getMessage()]);
                    return response()->json(['message' => 'Failed to store uploaded file', 'error' => $e->getMessage(), 'fallback' => $e2->getMessage()], 500);
                }
            }

            if (!$relativePath) {
                return response()->json(['message' => 'Failed to store uploaded file'], 500);
            }

            // Create DB record inside a transaction. If DB insert fails, remove the moved file to avoid orphaned files.
            DB::beginTransaction();
            try {
                $document = Document::create([
                    'name' => $request->name,
                    'type' => $request->type,
                    'file_path' => $relativePath,
                    'file_name' => $originalName,
                    'mime_type' => $mime,
                    'file_size' => $size,
                    'version' => $request->version,
                    'expiry_date' => $request->expiry_date,
                    'description' => $request->description,
                    'related_type' => $request->related_type,
                    'related_id' => $request->related_id,
                    'uploaded_by' => auth()->user()->id,
                ]);

                DB::commit();

                return response()->json($document->load('uploader'), 201);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Document DB insert failed, removing file', ['error' => $e->getMessage(), 'path' => $relativePath]);

                // remove the stored file to avoid orphans
                try {
                    if ($movedToPublic) {
                        $publicPath = public_path($relativePath);
                        if (file_exists($publicPath)) @unlink($publicPath);
                    } else {
                        Storage::disk('public')->delete($relativePath);
                    }
                } catch (\Exception $e2) {
                    Log::warning('Failed to delete orphaned uploaded file after DB failure', ['error' => $e2->getMessage(), 'path' => $relativePath]);
                }

                return response()->json(['message' => 'Failed to record uploaded file', 'error' => $e->getMessage()], 500);
            }
    }

    public function show(Document $document)
    {
        return response()->json($document->load(['uploader']));
    }

    public function download(Document $document)
    {
        // Support files stored under public/ (relative path in file_path)
        $publicPath = public_path($document->file_path);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $document->file_name);
        }
        // Fallback: try storage disk
        try {
            $path = Storage::disk('public')->path($document->file_path);
            if (file_exists($path)) return response()->download($path, $document->file_name);
        } catch (\Exception $e) {
            // ignore
        }
        abort(404);
    }

    public function destroy(Document $document)
    {
        // Try delete from public folder first
        $publicPath = public_path($document->file_path);
        if (file_exists($publicPath)) {
            @unlink($publicPath);
        } else {
            // fallback to storage disk
            try { Storage::disk('public')->delete($document->file_path); } catch (\Exception $e) { }
        }
        $document->delete();
        return response()->json(null, 204);
    }
}