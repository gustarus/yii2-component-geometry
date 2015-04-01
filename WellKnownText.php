<?php
/**
 * Created by:  Itella Connexions ©
 * Created at:  14:56 11.03.15
 * Developer:   Pavel Kondratenko
 * Contact:     gustarus@gmail.com
 */

namespace webulla\extensions\geometry;

use webulla\extensions\geometry\base\Geometry;
use webulla\extensions\geometry\base\GeometryCollection;
use webulla\extensions\geometry\geometries\LinearRing;
use webulla\extensions\geometry\geometries\LineString;
use webulla\extensions\geometry\geometries\MultiLineString;
use webulla\extensions\geometry\geometries\MultiPoint;
use webulla\extensions\geometry\geometries\MultiPolygon;
use webulla\extensions\geometry\geometries\Point;
use webulla\extensions\geometry\geometries\Polygon;

/**
 * Class WKT
 * @package webulla\geometry
 */
class WellKnownText {

	const POINT = 'point';
	const MULTIPOINT = 'multipoint';
	const LINESTRING = 'linestring';
	const MULTILINESTRING = 'multilinestring';
	const LINEARRING = 'linearring';
	const POLYGON = 'polygon';
	const MULTIPOLYGON = 'multipolygon';
	const GEOMETRYCOLLECTION = 'geometrycollection';

	/**
	 * @var array
	 */
	private $regExes = array(
		'type' => '/^\s*(\w+)\s*\(\s*(.*)\s*\)\s*$/',
		'spaces' => '/\s+/',
		'comma_parent' => '/\)\s*,\s*\(/',
		'comma_double_parent' => '/\)\s*\)\s*,\s*\(\s*\(/',
		'trim_parents' => '/^\s*\(*(.*?)\)*\s*$/'
	);


	/**
	 * Read WKT string into geometry objects
	 * @param string $WKT A WKT string
	 * @return Geometry|GeometryCollection
	 */
	public function read($WKT) {
		$matches = array();
		if(!preg_match($this->regExes['type'], $WKT, $matches)) {
			return null;
		}

		return $this->parse($matches[1], $matches[2]);
	}

	/**
	 * Parse WKT string into geometry objects
	 * @param string $type
	 * @param string $data
	 * @return GeometryCollection|LineString|LinearRing|MultiLineString|MultiPoint|MultiPolygon|Point|Polygon
	 */
	public function parse($type, $data) {
		switch(strtolower($type)) {
			case self::POINT:
				$Component = new Point();
				$coordinates = $this->pregExplode('spaces', $data);
				$Component->setCoordinates($coordinates[0], $coordinates[1]);

				return $Component;

			case self::MULTIPOINT:
				$Component = new MultiPoint();
				foreach(explode(',', trim($data)) as $point) {
					$Component->addComponent($this->parse(self::POINT, $point));
				}

				return $Component;

			case self::LINESTRING:
				$Component = new LineString();
				foreach(explode(',', trim($data)) as $point) {
					$Component->addComponent($this->parse(self::POINT, $point));
				}

				return $Component;

			case self::MULTILINESTRING:
				$Component = new MultiLineString();
				foreach($this->pregExplode('comma_parent', $data) as $line) {
					if($line = preg_replace($this->regExes['trim_parents'], '$1', $line)) {
						$Component->addComponent($this->parse(self::LINESTRING, $line));
					}
				}

				return $Component;

			case self::POLYGON:
				$Component = new Polygon();
				foreach($this->pregExplode('comma_parent', $data) as $ring) {
					if($ring = preg_replace($this->regExes['trim_parents'], '$1', $ring)) {
						$LineString = $this->parse(self::LINESTRING, $ring);
						$LinearRing = new LinearRing();
						$LinearRing->setComponents($LineString->getComponents());
						$Component->addComponent($LinearRing);
					}
				}

				return $Component;

			case self::MULTIPOLYGON:
				$Component = new MultiPolygon;
				foreach($this->pregExplode('comma_double_parent', $data) as $polygon) {
					if($polygon = preg_replace($this->regExes['trim_parents'], '$1', $polygon)) {
						$Component->addComponent($this->parse(self::POLYGON, $polygon));
					}
				}

				return $Component;

			case self::GEOMETRYCOLLECTION:
				$Component = new GeometryCollection();
				$data = preg_replace('/,\s*([A-Za-z])/', '|$1', $data);
				foreach(explode('|', trim($data)) as $wkt) {
					$Component->addComponent($this->read($wkt));
				}

				return $Component;
		}

		return null;
	}

	/**
	 * Split string according to first match of passed regEx index of $regExes
	 */
	protected function pregExplode($regEx, $data) {
		$matches = array();
		preg_match($this->regExes[$regEx], $data, $matches);

		return empty($matches) ? array(trim($data)) : explode($matches[0], trim($data));
	}

	/**
	 * Serialize geometries into a WKT string.
	 * @param Geometry $geometry
	 * @return string The WKT string representation of the input geometries
	 */
	public function write(Geometry $geometry) {
		$type = strtolower(get_class($geometry));

		if(is_null($data = $this->extract($geometry))) {
			return null;
		}

		return strtoupper($type) . '(' . $data . ')';
	}

	/**
	 * Extract geometry to a WKT string
	 * @param Geometry|GeometryCollection $geometry
	 * @return string
	 */
	public function extract($geometry) {
		$array = array();
		switch(strtolower($geometry->getType())) {
			case self::POINT:
				return implode(' ', $geometry->getCoordinates());

			case self::MULTIPOINT:
			case self::LINESTRING:
			case self::LINEARRING:
				foreach($geometry->getComponents() as $Component) {
					$array[] = $this->extract($Component);
				}

				return implode(',', $array);

			case self::MULTILINESTRING:
			case self::POLYGON:
			case self::MULTIPOLYGON:
				foreach($geometry->getComponents() as $Component) {
					$array[] = '(' . $this->extract($Component) . ')';
				}

				return implode(',', $array);

			case self::GEOMETRYCOLLECTION:
				foreach($geometry->getComponents() as $Component) {
					$array[] = strtoupper($Component->getType()) . '(' . $this->extract($Component) . ')';
				}

				return implode(',', $array);
		}

		return null;
	}

	/**
	 * Loads a WKT string into a Geometry Object
	 * @param string $data
	 * @return Geometry
	 */
	static public function load($data) {
		$instance = new self;

		return $instance->read($data);
	}

	/**
	 * Dumps a Geometry Object into a     WKT string
	 * @param Geometry $geometry
	 * @return String A WKT string corresponding to passed object
	 */
	static public function dump(Geometry $geometry) {
		$instance = new self;

		return $instance->write($geometry);
	}
}