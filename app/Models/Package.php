<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function items()
    {
        return $this->hasMany(PackageItem::class)->orderBy('sequence_order');
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'package_items')
                    ->withPivot('sequence_order')
                    ->orderByPivot('sequence_order');
    }
}
