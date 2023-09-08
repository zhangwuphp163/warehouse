<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 *  App\Models\Asn
 *
 * @property int $id
 * @property string $asn_number
 * @property string $status
 * @property string|null $inbound_at
 * @property string|null $confirmed_at
 * @property string|null $remarks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Asn onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn whereAsnNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn whereInboundAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Asn whereConfirmedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Asn withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Asn withoutTrashed()
 */
class Asn extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'asns';
    protected $fillable = ['asn_number','status','remarks','inbound_at','confirmed_at'];
    public function items(){
        return $this->hasMany(AsnItem::class);
    }

    public function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
