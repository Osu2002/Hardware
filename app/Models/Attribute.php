<?php
// app/Models/Attribute.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model {
  use SoftDeletes;
  protected $fillable = ['code','name','type','unit','is_filterable','is_variant_option','status','sort_order'];
  public function options(){ return $this->hasMany(AttributeOption::class); }
}

// app/Models/AttributeOption.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model {
  protected $fillable = ['attribute_id','value','hex','sort_order'];
  public function attribute(){ return $this->belongsTo(Attribute::class); }
}

