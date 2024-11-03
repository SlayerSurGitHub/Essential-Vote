<?php

namespace Essential\Vote\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

class PlayerVoteSuccessEvent extends Event implements Cancellable
{
    use CancellableTrait;

    public function __construct
    (
        private readonly Player $player,
    )
    {}

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

}