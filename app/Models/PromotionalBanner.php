<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalBanner extends Model
{
    use HasFactory;

    protected $table = 'c04_promotional_banner'; // Specify the table name if it's not pluralized
    protected $fillable = ['co4_banner', 'co4_page_link', 'c04_section_type']; // Specify fillable fields if needed
}
