<?php

namespace Vyxel;

use pocketmine\block\Block;
use pocketmine\entity\Item;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class main_vschematic extends PluginBase implements Listener {

    public $setPos = [];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function createCube(Position $center, float $radius) {
        $tree = [];
        //for ($x = )

        for($x = $center->x + $radius; $x >= $center->x - $radius; $x--) {
            for($y = $center->y + $radius; $y >= $center->y - $radius; $y--) {
                for($z = $center->z + $radius; $z >= $center->z - $radius; $z--) {
                    $getblock = $this->getServer()->getDefaultLevel()->getBlock(new Vector3($x, $y, $z))->getId();
                    if ($getblock !== 0)
                        $tree[] = ["x" => $center->x - $x, "y" => $center->y - $y, "z" => $center->z - $z, "id" => $getblock];
                    $this->getServer()->getDefaultLevel()->addParticle(new FlameParticle(new Vector3($x, $y, $z)));
                }
            }
        }
        file_put_contents($this->getDataFolder()."tree",json_encode($tree));
    }

    public function createObject(Player $player) {
        $player->sendMessage("Building in process..");
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $open = json_decode(file_get_contents($this->getDataFolder()."tree"), 1);
        for ($i=count($open);$i >= 0;$i--) {
            if (isset($open[$i])) {
                $this->getServer()->getDefaultLevel()->setBlock(
                    new Vector3(
                        $x + $open[$i]['x'],
                        $y + $open[$i]['y'],
                        $z + $open[$i]['z']
                    ), Block::get($open[$i]['id'])
                );
            }
        }
        $player->sendMessage("Building is ready");
    }

    public function onJooin(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $player->getId();
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        if ($player->getInventory()->getItemInHand()->getId() == 280) {
            $this->createObject($event->getPlayer());
        } else {
            $this->createCube(new Position($x, $y, $z, $this->getServer()->getDefaultLevel()), 7);
        }
    }
}
