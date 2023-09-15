<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @method static create(array $array)
 * @method static whereId(mixed $id)
 * @method static whereCode(mixed $code)
 */
class Client extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'clients';
    protected $fillable = [
        'code',
        'company_name',
        'shipper_name',
        'shipper_company',
        'shipper_phone',
        'shipper_address',
        'shipper_country',
        'shipper_province',
        'shipper_city',
        'shipper_postal_code',
        'shipper_email',
        'shipper_tax_number_type',
        'shipper_tax_number',
        'shipper_id_card_number_type',
        'shipper_id_card_number',
        'ioss_number',
        'ioss_issuer_country_code'
    ];
    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
