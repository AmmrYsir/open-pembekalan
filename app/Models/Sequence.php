<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
	protected $fillable = [
		'slug',
		'name',
		'format',
		'value',
		'daily_reset',
		'monthly_reset',
		'yearly_reset',
		'is_active'
	];
}
