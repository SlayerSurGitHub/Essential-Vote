<?php

declare(strict_types=1);

namespace Essential\Vote\library\invmenu\type\graphic\network;

use Essential\Vote\library\invmenu\session\InvMenuInfo;
use Essential\Vote\library\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		readonly private int $window_type
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->windowType = $this->window_type;
	}
}