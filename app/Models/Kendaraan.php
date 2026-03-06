<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
  use HasFactory;

  protected $fillable = [
    'nama',
    'alamat',
    'contact_person',
    'contact_phone',
  ];
}
