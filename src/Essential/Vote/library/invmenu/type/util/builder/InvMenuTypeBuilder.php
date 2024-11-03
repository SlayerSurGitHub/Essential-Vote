<?php

declare(strict_types=1);

namespace Essential\Vote\library\invmenu\type\util\builder;

use Essential\Vote\library\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}