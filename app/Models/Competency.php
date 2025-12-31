<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competency extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'code', 'description']; 
}