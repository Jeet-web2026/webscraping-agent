<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResearchRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['result' => 'array', 'filters' => 'array'];
    }

    public function productDetails(): HasOne
    {
        return $this->hasOne(ProductdetailsResponse::class);
    }
}
