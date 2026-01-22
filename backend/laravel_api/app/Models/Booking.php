<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $fillable = ['tenant_id', 'customer_name', 'customer_email', 'booking_date', 'status'];

    // Switch tenant connection dynamically

    public function setTenantConnection($connection)
    {
        $this->setConnection($connection);
        return $this;
    }
}
