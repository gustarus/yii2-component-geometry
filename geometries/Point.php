<?php
/**
 * Created by:  Itella Connexions ©
 * Created at:  14:57 11.03.15
 * Developer:   Pavel Kondratenko
 * Contact:     gustarus@gmail.com
 */

namespace webulla\extensions\geometry\geometries;

use webulla\extensions\geometry\base\Geometry;
use yii\base\Exception;

class Point extends Geometry {

	/**
	 * @inheritdoc
	 */
	protected $geometry_type = 'POINT';

	/**
	 * @var array
	 */
	private $_coordinates = array(2);


	/**
	 * @param float $x The x coordinate (or longitude)
	 * @param float $y The y coordinate (or latitude)
	 * @throws \yii\base\Exception
	 */
	public function setCoordinates($x, $y) {
		if(!is_numeric($x) || !is_numeric($y)) {
			throw new Exception("Bad coordinates: x and y should be numeric");
		}

		$this->_coordinates = array($x, $y);
	}

	/**
	 * @return array[double, double]
	 */
	public function getCoordinates() {
		return $this->_coordinates;
	}
}