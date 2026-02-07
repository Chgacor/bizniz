<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'message' => "Critical Stock: {$this->product->name} is down to {$this->product->stock_quantity} units.",
            'action_url' => route('warehouse.index'),
            'level' => 'critical'
        ];
    }
}
