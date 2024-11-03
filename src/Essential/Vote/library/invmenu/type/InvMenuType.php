<?php

declare(strict_types=1);

namespace Essential\Vote\library\invmenu\type;

use Essential\Vote\library\invmenu\InvMenu;
use Essential\Vote\library\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}