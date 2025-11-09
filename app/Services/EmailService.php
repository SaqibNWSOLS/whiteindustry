<?php

namespace App\Services;

use App\Models\{Quote, Order, Invoice, Lead};
use App\Mail\{QuotationMail, OrderConfirmationMail, InvoiceMail};
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send Quotation Email
     */
    public function sendQuotation($leadOrCustomer, Quote $quote, $pdfPath)
    {
        $email = $leadOrCustomer->email;
        $name = $leadOrCustomer->contact_person ?? $leadOrCustomer->company_name;

        Mail::to($email)->send(new QuotationMail($quote, $name, $pdfPath));
    }

    /**
     * Send Order Confirmation
     */
    public function sendOrderConfirmation(Order $order, $pdfPath)
    {
        $customer = $order->customer;
        
        Mail::to($customer->email)->send(new OrderConfirmationMail($order, $pdfPath));
    }

    /**
     * Send Invoice
     */
    public function sendInvoice(Invoice $invoice, $pdfPath)
    {
        $customer = $invoice->customer;
        
        Mail::to($customer->email)->send(new InvoiceMail($invoice, $pdfPath));
    }
}