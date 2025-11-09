<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $customerName;
    public $pdfPath;

    public function __construct(Quote $quote, $customerName, $pdfPath)
    {
        $this->quote = $quote;
        $this->customerName = $customerName;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject("Quotation {$this->quote->quote_number} - White Industry")
                    ->view('emails.quotation')
                    ->attach(storage_path('app/public/' . $this->pdfPath), [
                        'as' => "Quotation-{$this->quote->quote_number}.pdf",
                        'mime' => 'application/pdf',
                    ]);
    }
}