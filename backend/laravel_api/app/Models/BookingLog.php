<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    use HasFactory;

    protected $connection = 'analytics';
    protected $fillable = ['booking_id', 'action', 'changes'];
}
