<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductdetailsResponse extends Model
{
    protected $fillable = [
        "recent_photo",
        "video_link",
        "product_rate",
        "price",
        "feedback",
        "seller",
        "seller_address",
        "seller_contact_details",
        "website",
        "availability",
        'research_request_id'
    ];

    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }

    protected $casts = [
        'recent_photo' => 'array',
        'website'      => 'array',
    ];
}
