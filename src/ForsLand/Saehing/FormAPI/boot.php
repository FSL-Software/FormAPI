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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\plugin\PluginBase;

class boot extends PluginBase implements Listener {

	/** @var int */
	public $formCount = 0;
	/** @var array */
	public $forms = [];

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param callable $function
	 * @return CustomForm
	 */
	public function createCustomForm(callable $function = null) : CustomForm {
		$this->formCount++;
		$form = new CustomForm($this->formCount, $function);
		if($function !== null){
			$this->forms[$this->formCount] = $form;
		}
		return $form;
	}

	public function createSimpleForm(callable $function = null) : SimpleForm {
		$this->formCount++;
		$form = new SimpleForm($this->formCount, $function);
		if($function !== null){
			$this->forms[$this->formCount] = $form;
		}
		return $form;
	}

	/**
	 * @param DataPacketReceiveEvent $ev
	 */
	public function onPacketReceived(DataPacketReceiveEvent $ev) : void {
		$pk = $ev->getPacket();
		if($pk instanceof ModalFormResponsePacket){
			$player = $ev->getPlayer();
			$formId = $pk->formId;
			$data = json_decode($pk->formData, true);
			if(isset($this->forms[$formId])){
				/** @var Form $form */
				$form = $this->forms[$formId];
				if(!$form->isRecipient($player)){
					return;
				}
				$callable = $form->getCallable();
				if(!is_array($data)){
					$data = [$data];
				}
				if($callable !== null) {
					$callable($ev->getPlayer(), $data);
				}
				unset($this->forms[$formId]);
				$ev->setCancelled();
			}
		}
	}

	/**
	 * @param PlayerQuitEvent $ev
	 */
	public function onPlayerQuit(PlayerQuitEvent $ev){
		$player = $ev->getPlayer();
		/**
		 * @var int $id
		 * @var Form $form
		 */
		foreach($this->forms as $id => $form){
			if($form->isRecipient($player)){
				unset($this->forms[$id]);
				break;
			}
		}
	}

}
