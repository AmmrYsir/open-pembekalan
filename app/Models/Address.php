<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'postal_code',
        'district',
        'city',
        'state_id',
        'created_by',
        'updated_by',
    ];
}
