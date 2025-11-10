<?php
// app/Models/AttributeSet.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeSet extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','status','sort_order'];

    protected $casts = [
        'status'=>'boolean',
        'sort_order'=>'integer',
    ];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_set_attributes')
            ->withPivot(['is_required','sort_order'])
            ->withTimestamps();
    }
}
