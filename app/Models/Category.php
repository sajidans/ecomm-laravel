<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'c01_category'; // Specify the table name if it doesn't follow Laravel's conventions

    protected $fillable = ['c01_id', 'c01_name', 'c01_image', 'c01_banner_image', 'c01_status', 'c01_position'];
}
