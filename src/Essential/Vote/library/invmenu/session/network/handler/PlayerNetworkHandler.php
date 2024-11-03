<?php

declare(strict_types=1);

namespace Essential\Vote\library\invmenu\session\network\handler;

use Closure;
use Essential\Vote\library\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}