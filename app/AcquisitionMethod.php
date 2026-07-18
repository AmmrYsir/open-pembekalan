<?php

namespace App;

enum AcquisitionMethod: string
{
	case BEKALAN = 'BEKALAN';
	case PERKHIDMATAN = 'PERKHIDMATAN';
	case BEKALAN_DAN_PERKHIDMATAN = 'BEKALAN DAN PERKHIDMATAN';
}
