<?php

declare(strict_types=1);

namespace Essential\Vote\library\invmenu\session;

use Essential\Vote\library\invmenu\InvMenu;
use Essential\Vote\library\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}