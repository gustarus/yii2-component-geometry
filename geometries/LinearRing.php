<?php
/**
 * Created by:  Itella Connexions ©
 * Created at:  14:57 11.03.15
 * Developer:   Pavel Kondratenko
 * Contact:     gustarus@gmail.com
 */

namespace webulla\components\geometry\geometries;

use yii\base\Exception;

class LinearRing extends LineString {

	/**
	 * @inheritdoc
	 */
	protected $geometry_type = 'LINEARRING';


	/**
	 * @param \webulla\components\geometry\base\Geometry[] $components
	 * @throws \yii\base\Exception
	 */
	public function setComponents($components) {
		if(count($components)) {
			parent::setComponents($components);
		} else {
			throw new Exception('Linestring with less than two points');
		}
	}
} 