<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AsnItem extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['asn_id','material_id','supplier_id','plan_qty','actual_qty','plan_unit_price','actual_unit_price','inbound_at','confirmed_at'];
    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function material(){
        return $this->belongsTo(Material::class);
    }
}
