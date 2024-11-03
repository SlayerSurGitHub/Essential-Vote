<?php

namespace Essential\Vote\event\voteparty;

use pocketmine\event\Event;

class VotePartyReachedEvent extends Event
{
    public function __construct
    (
        private readonly int $voteGoal,
    )
    {}

    /**
     * @return int
     */
    public function getVoteGoal(): int
    {
        return $this->voteGoal;
    }

}