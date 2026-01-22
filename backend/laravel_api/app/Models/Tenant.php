<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'db_name', 'db_host', 'db_username', 'db_password'];
    protected $connection = 'mysql';
}
