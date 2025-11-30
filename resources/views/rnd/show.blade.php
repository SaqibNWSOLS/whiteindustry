
@extends('layouts.app')
@section('title', __('r_d.title'))

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
            <h2>@lang('r_d.review_page.title', ['quotation_number' => $rnd->quote->quotation_number])</h2>
            <span class="badge {{ $rnd->status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                @lang('r_d.status.' . $rnd->status)
            </span>
        </div>

        <!-- Body -->
        <div class="rnd-body">

            <!-- Quotation Info Section -->
            <div class="section">
                <h4>@lang('r_d.review_page.quotation_details')</h4>

                <p><strong>@lang('r_d.review_page.customer'):</strong> {{ $rnd->quote->customer->company_name }}</p>
                <p><strong>@lang('r_d.review_page.total_amount'):</strong> {{ priceFormat($rnd->quote->total_amount, 2) }}</p>
                <p><strong>@lang('r_d.review_page.products_count'):</strong> {{ $rnd->quote->products->count() }}</p>
            </div>

            <!-- Upload Documents -->
            <div class="section">
                <h4>@lang('r_d.review_page.upload_documents')</h4>

                @if($rnd->status !== 'approved')
                <form action="{{ route('rnd.upload', $rnd->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label><strong>@lang('r_d.review_page.select_files')</strong></label>
                    <input type="file" name="documents[]" multiple class="form-control" required>
                    <small class="text-muted">@lang('r_d.review_page.max_size')</small>
                    <br><br>
                    <button class="btn btn-primary">@lang('r_d.review_page.upload_button')</button>
                </form>
                @endif
            </div>

            <!-- Documents List -->
            <div class="section">
                <h4>@lang('r_d.review_page.documents_uploaded', ['count' => $rnd->documents->count()])</h4>

                @forelse($rnd->documents as $doc)
                    <div class="document-box">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $doc->document_name }}</strong><br>
                                <small>{{ $doc->file_type }} • @lang('r_d.document_info.file_size', ['size' => number_format($doc->file_size / 1024, 2)]) • @lang('r_d.document_info.uploaded_by', ['name' => $doc->uploadedBy->name])</small>
                            </div>
                            <div class="document-actions">
                                <a href="{{ asset($doc->file_path) }}" download class="btn btn-sm btn-secondary">
                                    @lang('r_d.review_page.download')
                                </a>
                                @if($rnd->status !== 'approved')
                                <form action="{{ route('rnd.document.delete', $doc->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('@lang('r_d.confirmations.delete_document')')">
                                        @lang('r_d.review_page.delete')
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p>@lang('r_d.review_page.no_documents')</p>
                @endforelse
            </div>

            <!-- Approve / Reject -->
            @if($rnd->status !== 'approved' && $rnd->documents->count() > 0)
            <div class="section">
                <h4>@lang('r_d.review_page.approve_reject')</h4>

                <div class="action-row">
                    <!-- Approve -->
                    <form action="{{ route('rnd.approve', $rnd->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <label><strong>@lang('r_d.review_page.rnd_notes_optional')</strong></label>
                        <textarea name="rnd_notes" class="form-control" rows="3" placeholder="@lang('r_d.review_page.add_notes_placeholder')"></textarea>
                        <br>
                        <button class="btn btn-success" onclick="return confirm('@lang('r_d.confirmations.approve_review')')">
                            @lang('r_d.review_page.approve_button')
                        </button>
                    </form>

                    <!-- Reject -->
                    <form action="{{ route('rnd.reject', $rnd->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <label><strong>@lang('r_d.review_page.rejection_reason')</strong></label>
                        <textarea name="rnd_notes" class="form-control" rows="3" required placeholder="@lang('r_d.review_page.reason_placeholder')"></textarea>
                        <br>
                        <button class="btn btn-danger" onclick="return confirm('@lang('r_d.confirmations.reject_review')')">
                            @lang('r_d.review_page.reject_button')
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($rnd->rnd_notes)
            <div class="section">
                <h4>@lang('r_d.review_page.rnd_notes')</h4>
                <p>{{ $rnd->rnd_notes }}</p>
            </div>
            @endif

        </div> <!-- end body -->

    </div>
</div>

@endsection