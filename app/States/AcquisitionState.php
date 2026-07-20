<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class AcquisitionState extends State
{
    abstract public function color(): string;

    public static function config(): StateConfig
    {
        return parent::config();
        // ->default(Draft::class)
        // ->allowTransition(Draft::class, Submitted::class)
        // ->allowTransition(Submitted::class, Approved::class)
        // ->allowTransition(Submitted::class, Rejected::class)
        // ->allowTransition(Approved::class, Completed::class);
    }
}
