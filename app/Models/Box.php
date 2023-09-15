<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @method static create(array $array)
 * @method static whereId(mixed $id)
 * @method static whereCode(mixed $value)
 * @method static whereWarehouseId(mixed $value)
 * @method static whereClientId(mixed $value)
 * @method static whereIsActive(mixed $value))
 */
class Box extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'boxes';
    protected $fillable = [
        'warehouse_id',
        'client_id',
        'code',
        'name',
        'length',
        'width',
        'height',
        'weight',
        'is_active'
    ];
    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
}
