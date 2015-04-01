<?php
/**
 * Created by:  Itella Connexions ©
 * Created at:  14:56 11.03.15
 * Developer:   Pavel Kondratenko
 * Contact:     gustarus@gmail.com
 */

namespace webulla\components\geometry\base;

abstract class Geometry {

	/**
	 * @return mixed
	 */
	abstract public function getCoordinates();

	/**
	 * Accessor for the geometry type
	 * @return string The Geometry type.
	 */
	public function getType() {
		$path = explode('\\', get_class($this));

		return end($path);
	}

	/**
	 * Returns an array suitable for serialization
	 * @return array
	 */
	public function getGeoInterface() {
		return array(
			'type' => $this->getType(),
			'coordinates' => $this->getCoordinates()
		);
	}
}