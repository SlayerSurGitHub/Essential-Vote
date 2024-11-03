<?php

declare(strict_types=1);

namespace Essential\Vote\library\invmenu\type\graphic\network;

use Essential\Vote\library\invmenu\session\InvMenuInfo;
use Essential\Vote\library\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}