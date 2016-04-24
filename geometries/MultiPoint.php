<?php
namespace webulla\extensions\geometry\geometries;

use webulla\extensions\geometry\base\Collection;

class MultiPoint extends Collection {

	/**
	 * @inheritdoc
	 */
	protected $geometry_type = 'MULTIPOINT';
}
