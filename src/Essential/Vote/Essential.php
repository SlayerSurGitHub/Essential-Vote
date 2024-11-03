<?php

namespace Essential\Vote;

use Essential\Vote\command\VoteCommand;
use Essential\Vote\library\invmenu\InvMenuHandler;
use Essential\Vote\listener\VoteListener;
use Essential\Vote\manager\VoteManager;
use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Essential extends PluginBase
{
    use SingletonTrait;

    private readonly Config $config;

    /**
     * @return void
     */
    protected function onLoad(): void
    {
        self::setInstance($this);

        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        @mkdir($this->getDataFolder() . "data");
        $this->saveResource("data/vote_party.json");
    }

    /**
     * @return void
     */
    protected function onEnable(): void
    {
        if (!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);

        new VoteManager($this);

        $this->getServer()->getCommandMap()->register("essential", new VoteCommand());
        $this->getServer()->getPluginManager()->registerEvents(new VoteListener($this), $this);
    }

    /**
     * @return void
     * @throws JsonException
     */
    protected function onDisable(): void
    {
        VoteManager::getInstance()->saveVoteParty();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

}