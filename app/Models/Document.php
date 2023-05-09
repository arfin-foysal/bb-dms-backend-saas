<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'catagory_id',
        'sub_catagory_id',
        'sub_sub_catagory_id',
        'description',
        'image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function catagory()
    {
        return $this->belongsTo(catagory::class);
    }

    public function subCatagory()
    {
        return $this->belongsTo(sub_catagory::class);
    }
    
    public function subSubCatagory()
    {
        return $this->belongsTo(sub_sub_catagory::class);
    }
  



}
