@extends('layouts.app')
@section('title', 'R&D Review')

@section('content')

<style>
    .rnd-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 25px;
    }

    .rnd-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .rnd-header {
        padding: 20px;
        background: #f7f9fc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rnd-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        color: #333;
    }

    .badge-success {
        background: #28a745;
        padding: 6px 12px;
        color: #fff;
        border-radius: 30px;
    }

    .badge-warning {
        background: #ffc107;
        padding: 6px 12px;
        color: #000;
        border-radius: 30px;
    }

    .rnd-body {
        padding: 25px;
    }

    .section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .section h4 {
        margin-bottom: 15px;
        font-weight: 600;
        font-size: 18px;
        color: #444;
    }

    .document-box {
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 10px;
        background: #fafafa;
    }

    .document-box strong {
        color: #333;
    }

    .document-box small {
        color: #777;
    }

    textarea, input[type="file"] {
        margin-top: 8px;
    }

    .btn-primary,
    .btn-success,
    .btn-danger,
    .btn-secondary {
        padding: 8px 14px;
        font-size: 14px;
        border-radius: 6px !important;
    }

    .action-row {
        display: flex;
        gap: 20px;
    }

    .document-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .action-row {
            flex-direction: column;
        }
        
        .document-actions {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>


<div class="rnd-wrapper">
    <div class="rnd-card">

        <!-- Header -->
        <div class="rnd-header">
            <h2>R&D Review — {{ $rnd->quote->quotation_number }}</h2>
            <span class="badge {{ $rnd->status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                {{ ucfirst($rnd->status) }}
            </span>
        </div>

        <!-- Body -->
        <div class="rnd-body">

            <!-- Quotation Info Section -->
            <div class="section">
                <h4>Quotation Details</h4>

                <p><strong>Customer:</strong> {{ $rnd->quote->customer->company_name }}</p>
                <p><strong>Total Amount:</strong> ${{ number_format($rnd->quote->total_amount, 2) }}</p>
                <p><strong>Products:</strong> {{ $rnd->quote->products->count() }}</p>
            </div>

            <!-- Upload Documents -->
            <div class="section">
                <h4>Upload R&D Documents</h4>

                @if($rnd->status !== 'approved')
                <form action="{{ route('rnd.upload', $rnd->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label><strong>Select Files (PDF, DOC, Excel)</strong></label>
                    <input type="file" name="documents[]" multiple class="form-control" required>
                    <small class="text-muted">Max 5MB per file</small>
                    <br><br>
                    <button class="btn btn-primary">Upload Documents</button>
                </form>
                @endif
            </div>

            <!-- Documents List -->
            <div class="section">
                <h4>Documents Uploaded ({{ $rnd->documents->count() }})</h4>

                @forelse($rnd->documents as $doc)
                    <div class="document-box">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $doc->document_name }}</strong><br>
                                <small>{{ $doc->file_type }} • {{ number_format($doc->file_size / 1024, 2) }} KB • Uploaded by {{ $doc->uploadedBy->name }}</small>
                            </div>
                            <div class="document-actions">
                                <a href="{{ Storage::url($doc->file_path) }}" download class="btn btn-sm btn-secondary">
                                    Download
                                </a>
                                @if($rnd->status !== 'approved')
                                <form action="{{ route('rnd.document.delete', $doc->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this document?')">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No documents uploaded yet.</p>
                @endforelse
            </div>

            <!-- Approve / Reject -->
            @if($rnd->status !== 'approved' && $rnd->documents->count() > 0)
            <div class="section">
                <h4>Approve or Reject</h4>

                <div class="action-row">
                    <!-- Approve -->
                    <form action="{{ route('rnd.approve', $rnd->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <label><strong>R&D Notes (Optional)</strong></label>
                        <textarea name="rnd_notes" class="form-control" rows="3" placeholder="Add notes..."></textarea>
                        <br>
                        <button class="btn btn-success" onclick="return confirm('Approve this R&D review?')">
                            Approve
                        </button>
                    </form>

                    <!-- Reject -->
                    <form action="{{ route('rnd.reject', $rnd->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <label><strong>Rejection Reason</strong></label>
                        <textarea name="rnd_notes" class="form-control" rows="3" required placeholder="Reason..."></textarea>
                        <br>
                        <button class="btn btn-danger" onclick="return confirm('Reject this R&D review?')">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($rnd->rnd_notes)
            <div class="section">
                <h4>R&D Notes</h4>
                <p>{{ $rnd->rnd_notes }}</p>
            </div>
            @endif

        </div> <!-- end body -->

    </div>
</div>

@endsection