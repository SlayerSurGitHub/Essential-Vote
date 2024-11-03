<?php

namespace Essential\Vote\manager\feature;

use Essential\Vote\Essential;
use JsonException;
use pocketmine\utils\Config;

abstract class VoteParty
{
    private Config $votePartyData;

    private int $voteGoal;
    private int $voteCount;

    /**
     * @param int $voteGoal
     * @return bool
     */
    protected function initVoteParty(int $voteGoal): bool
    {
        if ($voteGoal < 1) return false;

        $this->votePartyData = new Config(Essential::getInstance()->getDataFolder() . "data/vote_party.json", Config::JSON);

        try {
            $this->voteCount = $this->votePartyData->get("votePartyCount", 0);
        } catch (\Exception $exception)
        {
            $this->voteCount = 0;
        }

        $this->voteGoal = $voteGoal;
        return true;
    }

    /**
     * @throws JsonException
     */
    public function saveVoteParty(): void
    {
        $votePartyData = $this->votePartyData;

        $votePartyData->set("votePartyCount", $this->voteCount ?? 0);
        $votePartyData->save();
    }

    /**
     * @param int $voteCount
     * @return void
     */
    public function setVoteCount(int $voteCount = 0): void
    {
        $this->voteCount = $voteCount;
    }

    /**
     * @param int $voteCount
     * @return bool
     */
    protected function addVoteCount(int $voteCount = 1): bool
    {
        $newVoteCount = $this->voteCount + $voteCount;

        if ($newVoteCount >= $this->voteGoal) return true;

        $this->voteCount += $voteCount;
        return false;
    }

    /**
     * @return int
     */
    public function getVoteCount(): int
    {
        return $this->voteCount;
    }

    /**
     * @return int
     */
    public function getVoteGoal(): int
    {
        return $this->voteGoal;
    }

}