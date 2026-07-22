<?php

namespace App\States;

use App\Models\Acquisition;
use App\States\Acquisition\CommitteeAppointment;
use App\States\Acquisition\Draft;
use App\States\Acquisition\Verified;
use App\States\Acquisition\Approved;
use App\States\Transitions\UpdateAssignment;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends State<Acquisition>
 */
abstract class AcquisitionState extends State
{
    abstract public function color(): string;

    abstract public function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Verified::class, UpdateAssignment::class)
            ->allowTransition(Verified::class, Approved::class, UpdateAssignment::class)
            ->allowTransition(Approved::class, CommitteeAppointment::class, UpdateAssignment::class);
    }
}
