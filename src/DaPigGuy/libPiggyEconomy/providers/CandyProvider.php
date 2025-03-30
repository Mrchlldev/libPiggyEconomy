<?php

declare(strict_types=1);

namespace DaPigGuy\libPiggyEconomy\providers;

use Mrchlldev\CandyAPI\CandyAPI;
use pocketmine\entity\utils\ExperienceUtils;
use pocketmine\player\Player;

class XPProvider extends EconomyProvider
{
    public function getMonetaryUnit(): string
    {
        return "Candy";
    }

    public function __construct()
    {
        $this->candyAPI = CandyAPI::getInstance();
    }

    public static function checkDependencies(): bool
    {
        return Server::getInstance()->getPluginManager()->getPlugin("CandyAPI") !== null;
    }

    public function getMoney(Player $player, callable $callback): void
    {
        $callback($this->candyAPI->getCandy($player) ?: 0);
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $ret = $this->candyAPI->addCandy($player, $amount);
        if ($callback) $callback($ret);
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $ret = $this->candyAPI->takeCandy($player, $amount);
        if ($callback) $callback($ret);
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $ret = $this->candyAPI->setCandy($player, $amount);
        if ($callback) $callback($ret);
    }
}