<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inquiry extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['name','email', 'phone', 'address', 'payment_method', 'purchase_time', 'url', 'vehicle_id','type', 'status' ];
    protected $with = ['media'];
    protected $table = 'inquiries';

    public $inquiryLeads = [
        [
            'id' => 1,
            'name' => 'Pending'
        ],
        [
            'id' => 2,
            'name' => 'Client Contacted'
        ],
        [
            'id' => 3,
            'name' => 'Nigotiating'
        ],
        [
            'id' => 4,
            'name' => 'Advance Paid'
        ],
        [
            'id' => 5,
            'name' => 'Awaiting for Shipping'
        ],
        [
            'id' => 6,
            'name' => 'Completed'
        ],
    ];
}
