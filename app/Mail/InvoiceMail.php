<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfPath;

    public function __construct(Invoice $invoice, $pdfPath)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject("Invoice {$this->invoice->invoice_number} - White Industry")
                    ->view('emails.invoice')
                    ->attach(storage_path('app/public/' . $this->pdfPath), [
                        'as' => "Invoice-{$this->invoice->invoice_number}.pdf",
                        'mime' => 'application/pdf',
                    ]);
    }
}