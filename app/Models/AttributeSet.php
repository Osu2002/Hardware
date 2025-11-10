<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    protected $fillable = ['name','status','sort_order'];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_set_attributes')
            ->withPivot(['is_required','sort_order'])
            ->orderBy('attribute_set_attributes.sort_order');
    }
}
