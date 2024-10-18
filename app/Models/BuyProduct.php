<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyProduct extends Model
{
    use HasFactory;

    protected $table = 'm27_buy_product';
    protected $primaryKey = 'm_buy_prod_id';
}
