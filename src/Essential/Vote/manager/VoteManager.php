<?php

namespace Essential\Vote\manager;

use Essential\Vote\Essential;
use Essential\Vote\event\PlayerVoteFailEvent;
use Essential\Vote\event\PlayerVoteSuccessEvent;
use Essential\Vote\event\voteparty\VotePartyReachedEvent;
use Essential\Vote\manager\feature\VoteParty;
use pocketmine\player\Player;
use pocketmine\utils\ConfigLoadException;
use pocketmine\utils\Internet;
use pocketmine\utils\SingletonTrait;

class VoteManager extends VoteParty
{
    use SingletonTrait;

    public function __construct
    (
        private readonly Essential $main
    )
    {
        self::setInstance($this);

        try {
            if (!$this->initVoteParty($this->main->getConfig()->get("vote-party")["vote-goal"])) throw new ConfigLoadException("Vote party hasn't been configured");
        } catch (\Exception $exception)
        {
            $this->initVoteParty(50);
        }
    }

    /**
     * @param Player $player
     * @param int $voteResult
     * @return void
     */
    public function handleVoteRequest(Player $player, int $voteResult): void
    {
        $actions = [
            0 => fn(Player $player) => (new PlayerVoteFailEvent($player, PlayerVoteFailEvent::CAUSE_NONE))->call(),
            1 => function(Player $player): void
            {
                Internet::simpleCurl("https://minecraftpocket-servers.com/api/?action=post&object=votes&element=claim&key=" . $this->main->getConfig()->get("vote-web-key") . "&username=" . $player->getName());

                $voteSuccessEvent = new PlayerVoteSuccessEvent($player);
                $voteSuccessEvent->call();

                if ($voteSuccessEvent->isCancelled()) return;

                if ($this->addVoteCount()) {
                    $voteGoal = $this->getVoteCount();

                    $this->setVoteCount();
                    (new VotePartyReachedEvent($voteGoal))->call();
                }
            },
            2 => fn(Player $player) => (new PlayerVoteFailEvent($player, PlayerVoteFailEvent::CAUSE_ALREADY_VOTED))->call()
        ];

        $actions[$voteResult] ??= fn() => null;
        $actions[$voteResult]($player);
    }

}