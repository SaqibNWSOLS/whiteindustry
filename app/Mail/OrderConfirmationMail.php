<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdfPath;

    public function __construct(Order $order, $pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject("Order Confirmation {$this->order->order_number} - White Industry")
                    ->view('emails.order-confirmation')
                    ->attach(storage_path('app/public/' . $this->pdfPath), [
                        'as' => "Order-{$this->order->order_number}.pdf",
                        'mime' => 'application/pdf',
                    ]);
    }
}
