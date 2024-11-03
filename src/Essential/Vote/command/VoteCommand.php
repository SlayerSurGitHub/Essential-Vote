<?php

namespace Essential\Vote\command;

use Essential\Vote\async\VoteRequestAsyncTask;
use Essential\Vote\Essential;
use Essential\Vote\library\invmenu\InvMenu;
use Essential\Vote\library\invmenu\transaction\DeterministicInvMenuTransaction;
use Essential\Vote\library\invmenu\type\InvMenuTypeIds;
use Essential\Vote\manager\VoteManager;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class VoteCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "vote",
            "Vote for this server",
            "/vote"
        );

        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player)
        {
            $sender->sendMessage("§cVous ne pouvez pas éxécuter cette commande via la console !");
            return false;
        }

        $nbtToItemCallBack = function (string $nbtName, string $value, Item $item): Item
        {
            $item->getNamedTag()->setString($nbtName, $value);
            return $item;
        };
        $inventory = InvMenu::create(InvMenuTypeIds::TYPE_CHEST)->setName("Vote");
        foreach (array_fill_keys([1, 7, 9, 17, 19, 25], VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN)->asItem()) as $index => $item) $inventory->getInventory()->setItem($index, $item);
        foreach (array_fill_keys([0, 8, 18, 26], VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME)->asItem()) as $index => $item) $inventory->getInventory()->setItem($index, $item);
        $mainItemsContent = [
            12 => $nbtToItemCallBack("itemUtility", "vote", (StringToItemParser::getInstance()->parse("chest_minecart") ?? VanillaItems::MINECART())->setCustomName("§r§a§lCliquer pour voter")),
            14 => $nbtToItemCallBack("itemUtility", "voteParty", VanillaItems::NETHER_STAR()->setCustomName("§r§e§lVoteParty\n\n§r§7" . VoteManager::getInstance()->getVoteCount() . "/" . VoteManager::getInstance()->getVoteGoal() . " vote(s)"))
        ];
        $inventory->getInventory()->setContents(array_replace($inventory->getInventory()->getContents(), $mainItemsContent));
        $inventory->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use (&$inventory): void
        {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if (is_null($item->getNamedTag()->getTag("itemUtility"))) return;

            $itemTag = $item->getNamedTag()->getString("itemUtility");
            switch ($itemTag)
            {
                case "vote":
                    $inventory->onClose($player);

                    $pk = new PlaySoundPacket();
                    [$pk->x, $pk->y, $pk->z] = [$player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()];
                    [$pk->pitch, $pk->volume] = [0.80, 5];
                    $pk->soundName = "random.levelup";
                    $player->getNetworkSession()->sendDataPacket($pk);

                    $player->sendMessage("§aVotre requête est en cours de vérification...");
                    Essential::getInstance()->getServer()->getAsyncPool()->submitTask(new VoteRequestAsyncTask($player->getName(), Essential::getInstance()->getConfig()->get("vote-web-key")));
                    break;
                default:
                    break;
            }
        }));
        $inventory->send($sender);
        return true;
    }

}