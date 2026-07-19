<?php

namespace App\Actions;

use Illuminate\Support\Str;

class GetAbbreviation {
	
	public static function execute(string $word): string {
		return Str::of($word)
			->explode(' ')
			->map(fn($word) => Str::substr($word, 0, 1))
			->join('');
	}

}