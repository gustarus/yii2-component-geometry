<?php
/**
 * Created by:  Itella Connexions ©
 * Created at:  14:56 11.03.15
 * Developer:   Pavel Kondratenko
 * Contact:     gustarus@gmail.com
 */

namespace webulla\components\geometry\base;

use Iterator;

abstract class Collection extends Geometry {

	/**
	 * @var Geometry[]
	 */
	private $_components = [];


	/**
	 * @param Geometry[] $components
	 */
	public function setComponents($components) {
		$this->_components = $components;
	}

	/**
	 * @return Geometry[]
	 */
	public function getComponents() {
		return $this->_components;
	}

	/**
	 * @param Geometry[] $components
	 */
	public function mergeComponents($components) {
		foreach($components as $component) {
			$this->addComponent($component);
		}
	}

	/**
	 * @param $component
	 */
	public function addComponent($component) {
		$this->_components[] = $component;
	}


	/**
	 * An accessor method which recursively calls itself to build the coordinates array
	 * @return array The coordinates array
	 */
	public function getCoordinates() {
		$coordinates = array();
		foreach($this->_components as $component) {
			$coordinates[] = $component->getCoordinates();
		}

		return $coordinates;
	}
}