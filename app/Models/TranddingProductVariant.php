<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranddingProductVariant extends Model
{
    use HasFactory;

    protected $table = 'p02_product_variant';
    protected $primaryKey = 'pr_vari_id';
}
