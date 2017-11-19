<?php

/**
  *   _______             _
  *  |  _____|           | |                     _
  *  | |__               | |                    | |
  *  | ___|__  _ ___ ___ | |     __ _  _ __   __| | 
  *  | | / _ \| '__// __|| |    / _` || '_ \ / _  |
  *  | || (_) | |   \__ \| |___| (_| || | | | (_| |
  *  |_| \___/|_|   |___/|_____|\__,_||_| |_|\__,_|
  *
  * Based on: https://github.com/jojoe77777/FormAPI
  *
  * @Author: Saehing
  * @Website: ForsLand.ru
  * @Vk: vk.com/fors_land_mcpe
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *
  *
  *
  * Copyright (C) 2017 ForsLand
  *
  **/

declare(strict_types = 1);

namespace ForsLand\Saehing\FormAPI;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

abstract class Form {

	/** @var int */
	public $id;
	/** @var array */
	private $data = [];
	/** @var string */
	public $playerName;
	/** @var callable */
	private $callable;

	/**
	 * @param int $id
	 * @param callable $callable
	 */
	public function __construct(int $id, ?callable $callable) {
		$this->id = $id;
		$this->callable = $callable;
	}

	/**
	 * @return int
	 */
	public function getId() : int {
		return $this->id;
	}

	/**
	 * @param Player $player
	 */
	public function sendToPlayer(Player $player) : void {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->id;
		$pk->formData = json_encode($this->data);
		$player->dataPacket($pk);
		$this->playerName = $player->getName();
	}

	public function isRecipient(Player $player) : bool {
		return $player->getName() === $this->playerName;
	}

	public function getCallable() : ?callable {
		return $this->callable;
	}

}
