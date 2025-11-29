@extends('layouts.app')
@section('title', __('quality_control.title'))

@section('content')

<style>
    .qa-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 25px;
    }

    .qa-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .qa-header {
        padding: 20px;
        background: #f7f9fc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .qa-header h2 {
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

    .qa-body {
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


<div class="qa-wrapper">
    <div class="qa-card">

        <!-- Header -->
        <div class="qa-header">
            <h2>@lang('quality_control.review_page.title', ['order_number' => $qa->order->order_number])</h2>
            <span class="badge {{ $qa->status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                @lang('quality_control.status.' . $qa->status)
            </span>
        </div>

        <!-- Body -->
        <div class="qa-body">

            <!-- Quote details -->
            <div class="section">
                <h4>@lang('quality_control.review_page.quotation_details')</h4>
                <p><strong>@lang('quality_control.review_page.customer'):</strong> {{ $qa->order->customer->company_name }}</p>
                <p><strong>@lang('quality_control.review_page.total_amount'):</strong> ${{ number_format($qa->order->total_amount, 2) }}</p>
                <p><strong>@lang('quality_control.review_page.rnd_status'):</strong> 
                    <span class="badge badge-success">@lang('quality_control.status.' . $qa->rndQuote->status)</span>
                </p>
            </div>

            <!-- R&D Documents -->
            <div class="section">
                <h4>@lang('quality_control.review_page.rnd_documents_review')</h4>

                @forelse($qa->rndQuote->documents as $doc)
                    <div class="document-box">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong>{{ $doc->document_name }}</strong>
                            <a href="{{ Storage::url($doc->file_path) }}" class="btn btn-sm btn-secondary" download>
                                @lang('quality_control.review_page.download')
                            </a>
                        </div>
                    </div>
                @empty
                    <p>@lang('quality_control.review_page.no_rnd_documents')</p>
                @endforelse
            </div>

            <!-- Upload QA docs -->
            <div class="section">
                <h4>@lang('quality_control.review_page.upload_documents')</h4>

                @if($qa->status !== 'approved')
                <form action="{{ route('qa.upload', $qa->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label><strong>@lang('quality_control.review_page.select_files')</strong></label>
                    <input type="file" name="documents[]" multiple class="form-control" required>
                    <br>
                    <button type="submit" class="btn btn-primary">@lang('quality_control.review_page.upload_button')</button>
                </form>
                @endif
            </div>

            <!-- QA Uploaded Docs -->
            <div class="section">
                <h4>@lang('quality_control.review_page.documents_uploaded', ['count' => $qa->documents->count()])</h4>

                @forelse($qa->documents as $doc)
                    <div class="document-box">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $doc->document_name }}</strong><br>
                                <small>{{ $doc->file_type }} • @lang('quality_control.document_info.file_size', ['size' => number_format($doc->file_size / 1024, 2)])</small>
                            </div>
                            <div class="document-actions">
                                <a href="{{ Storage::url($doc->file_path) }}" download class="btn btn-sm btn-secondary">
                                    @lang('quality_control.review_page.download')
                                </a>
                                @if($qa->status !== 'approved')
                                <form action="{{ route('qa.document.delete', $doc->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('@lang('quality_control.confirmations.delete_document')')">
                                        @lang('quality_control.review_page.delete')
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p>@lang('quality_control.review_page.no_documents')</p>
                @endforelse
            </div>

            <!-- Approve / Reject -->
            @if($qa->status !== 'approved' && $qa->documents->count() > 0)
            <div class="section">
                <h4>@lang('quality_control.review_page.approve_reject')</h4>

                <div class="action-row">

                    <!-- Approve -->
                    <form action="{{ route('qa.approve', $qa->id) }}" method="POST" style="flex:1;">
                        @csrf
                        <label><strong>@lang('quality_control.review_page.qa_notes_optional')</strong></label>
                        <textarea name="qa_notes" class="form-control" rows="3"></textarea>
                        <br>
                        <button type="submit" class="btn btn-success" onclick="return confirm('@lang('quality_control.confirmations.approve_qa')')">
                            @lang('quality_control.review_page.approve_button')
                        </button>
                    </form>

                    <!-- Reject -->
                    <form action="{{ route('qa.reject', $qa->id) }}" method="POST" style="flex:1;">
                        @csrf
                        <label><strong>@lang('quality_control.review_page.rejection_reason')</strong></label>
                        <textarea name="qa_notes" class="form-control" rows="3" required></textarea>
                        <br>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('quality_control.confirmations.reject_qa')')">
                            @lang('quality_control.review_page.reject_button')
                        </button>
                    </form>

                </div>
            </div>
            @endif

        </div> <!-- end body -->

    </div>
</div>

@endsection