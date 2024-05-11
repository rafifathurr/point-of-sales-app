<?php

namespace App\Models\SalesOrder;

use App\Models\Product\ProductSize;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $table = 'sales_order_item';
    protected $guarded = [];

    public function productSize()
    {
        return $this->hasOne(ProductSize::class, 'id', 'product_size_id');
    }

    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class, 'id', 'sales_order_id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function deletedBy()
    {
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
}
