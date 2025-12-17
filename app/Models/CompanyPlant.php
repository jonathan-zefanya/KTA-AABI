<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPlant extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'type', 'address'];
    
    protected $casts = [
        'company_id' => 'integer',
        'type' => 'string'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
