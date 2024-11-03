<?php

namespace Essential\Vote\async;

use Essential\Vote\manager\VoteManager;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class VoteRequestAsyncTask extends AsyncTask
{
    public function __construct
    (
        private readonly string $playerName,
        private readonly string $voteKey,
    )
    {}

    /**
     * @return void
     */
    public function onRun(): void
    {
        $requestUrl = "https://minecraftpocket-servers.com/api/?object=votes&element=claim&key={$this->voteKey}&username={$this->playerName}&secret=%secret%}";
        $this->setResult(Internet::getURL($requestUrl)?->getBody());
    }

    /**
     * @return void
     */
    public function onCompletion(): void
    {
        $player = Server::getInstance()->getPlayerExact($this->playerName);

        if (!$player instanceof Player) return;

        VoteManager::getInstance()->handleVoteRequest($player, $this->getResult());
    }

}