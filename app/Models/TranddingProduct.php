<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranddingProduct extends Model
{
    use HasFactory;

    protected $table = 'm16_products';
    protected $primaryKey = 'm16_id';
}
