<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Committee extends Model
{
	use Uuids;

	protected $fillable = [
		'uuid',
		'slug',
		'name',
		'position'
	];

	protected $casts = [
		'position' => 'array'
	];
}
