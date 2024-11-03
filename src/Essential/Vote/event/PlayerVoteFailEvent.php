<?php

namespace Essential\Vote\event;

use pocketmine\event\Event;
use pocketmine\player\Player;

class PlayerVoteFailEvent extends Event
{
    const CAUSE_NONE = 0;
    const CAUSE_ALREADY_VOTED = 1;

    public function __construct
    (
        private readonly Player $player,
        private readonly int $cause
    )
    {}

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return int
     */
    public function getCause(): int
    {
        return $this->cause;
    }

}