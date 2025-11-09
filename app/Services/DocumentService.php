<?php

namespace App\Services;

use App\Models\{Quote, Order, Invoice, Lead};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Generate Quote PDF
     */
    public function generateQuotePDF(Quote $quote, $leadOrCustomer = null)
    {
        $data = [
            'quote' => $quote->load('product'),
            'lead' => $leadOrCustomer,
            'company' => $this->getCompanyInfo(),
        ];

        $pdf = Pdf::loadView('pdf.quote', $data);
        
        $filename = "quotes/quote-{$quote->quote_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        
        return $filename;
    }

    /**
     * Generate Order PDF
     */
    public function generateOrderPDF(Order $order)
    {
        $data = [
            'order' => $order->load(['customer', 'product']),
            'company' => $this->getCompanyInfo(),
        ];

        $pdf = Pdf::loadView('pdf.order', $data);
        
        $filename = "orders/order-{$order->order_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        
        return $filename;
    }

    /**
     * Generate Invoice PDF
     */
    public function generateInvoicePDF(Invoice $invoice)
    {
        $data = [
            'invoice' => $invoice->load(['customer', 'order', 'payments']),
            'company' => $this->getCompanyInfo(),
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        
        $filename = "invoices/invoice-{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        
        return $filename;
    }

    /**
     * Get company information
     */
    protected function getCompanyInfo()
    {
        return [
            'name' => 'White Industry',
            'address' => '123 Industrial Zone, Algiers, Algeria',
            'phone' => '+213 21 123 456',
            'email' => 'contact@whiteindustry.com',
            'tax_id' => 'DZ-123456789',
            'logo' => public_path('images/logo.png'),
        ];
    }
}
