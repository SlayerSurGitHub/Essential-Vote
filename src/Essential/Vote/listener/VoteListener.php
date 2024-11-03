<?php

namespace Essential\Vote\listener;

use Essential\Vote\Essential;
use Essential\Vote\event\PlayerVoteFailEvent;
use Essential\Vote\event\PlayerVoteSuccessEvent;
use Essential\Vote\event\voteparty\VotePartyReachedEvent;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\lang\Language;
use pocketmine\player\Player;
use pocketmine\Server;

class VoteListener implements Listener
{
    public function __construct
    (
        private readonly Essential $main,
    )
    {}

    /**
     * @param PlayerVoteSuccessEvent $event
     * @return void
     */
    public function handlePlayerVoteSuccessEvent(PlayerVoteSuccessEvent $event): void
    {
        $player = $event->getPlayer();
        $rewards = [];

        foreach ($this->main->getConfig()->get("vote-rewards") ?? [] as $values)
        {
            $rewards[] = $values["name"];
            $this->main->getServer()->dispatchCommand(
                new ConsoleCommandSender($this->main->getServer(), new Language(Language::FALLBACK_LANGUAGE)),
                str_replace("{username}", $player->getName(), $values["command"])
            );
        }

        $player->sendMessage("§aVous venez de voter pour le serveur et vous gagnez " . implode(", ", $rewards) . " !");
        Server::getInstance()->broadcastMessage("§2[§aVote§2] §a{$player->getName()} vient de voter pour le serveur avec la commande /vote !");
    }

    /**
     * @param PlayerVoteFailEvent $event
     * @return void
     */
    public function handlePlayerVoteFailEvent(PlayerVoteFailEvent $event): void
    {
        $player = $event->getPlayer();
        $cause = $event->getCause();

        if ($cause == $event::CAUSE_NONE)
        {
            $player->sendMessage("§cVous n'avez pas voter pour le serveur !");
            return;
        }

        if ($cause == $event::CAUSE_ALREADY_VOTED)
        {
            $player->sendMessage("§cVous avez déjà voter pour le serveur !");
            return;
        }

        $player->sendMessage("§cErreur inconnu lors de la comptabilité de votre vote.");
    }

    /**
     * @param VotePartyReachedEvent $event
     * @return void
     */
    public function handleVotePartyReachedEvent(VotePartyReachedEvent $event): void
    {
        $voteGoal = $event->getVoteGoal();

        array_map(function (Player $player) use ($voteGoal): void
        {
            foreach ($this->main->getConfig()->get("vote-party")["rewards"] ?? [] as $values)
            {
                $this->main->getServer()->dispatchCommand(
                    new ConsoleCommandSender($this->main->getServer(), new Language(Language::FALLBACK_LANGUAGE)),
                    str_replace("{username}", $player->getName(), $values["command"])
                );
            }

            $player->sendTitle("§rVoteParty", "§7{$voteGoal} vote(s) atteint(s)");
        }, $this->main->getServer()->getOnlinePlayers());

        Server::getInstance()->broadcastMessage("§aLe serveur vient d'atteindre le VoteParty qui était de {$voteGoal} vote(s) !");
    }

}