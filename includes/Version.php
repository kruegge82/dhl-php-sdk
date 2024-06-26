<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 26.01.2017
 * Time: 15:04
 *
 * Notes: Contains all Version-Specific Functions
 */

use stdClass;

/**
 * Class Version
 *
 * @package Jahn\DHL
 */
abstract class Version {
	/**
	 * Current-Version
	 *
	 * @var string $version - Current-Version
	 */
	private $version;

	/**
	 * Mayor-Version-Number
	 *
	 * @var int $mayor - Mayor-Version-Number
	 */
	private $mayor;

	/**
	 * Minor-Version-Number
	 *
	 * @var int $minor - Minor-Version-Number
	 */
	private $minor;

	/**
	 * Version constructor.
	 *
	 * @param string $version - Version of the DHL-API, you want to use
	 */
	protected function __construct($version) {
		$this->setVersion($version);
	}

	/**
	 * Getter for Current-Version
	 *
	 * @return string - Current-Version
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Set/Change the Version and also Update Mayor and Minor-Fields
	 *
	 * @param string $version - Version
	 */
	protected function setVersion($version) {
		$this->version = $version;

		$numbers = explode('.', $version);

		// Update Mayor and Minor-Version-Numbers
		$this->setMayor((int) $numbers[0]);
		$this->setMinor((int) $numbers[1]);
	}

	/**
	 * Getter for Mayor-Version-Number
	 *
	 * @return int - Mayor-Version-Number
	 */
	public function getMayor() {
		return $this->mayor;
	}

	/**
	 * Setter for Mayor-Version-Number
	 *
	 * @param int $mayor - Mayor-Version-Number
	 */
	public function setMayor($mayor) {
		$this->mayor = $mayor;
	}

	/**
	 * Getter for Minor-Version-Number
	 *
	 * @return int - Minor-Version-Number
	 */
	public function getMinor() {
		return $this->minor;
	}

	/**
	 * Setter for Minor-Version-Number
	 *
	 * @param int $minor - Minor-Version-Number
	 */
	public function setMinor($minor) {
		$this->minor = $minor;
	}

	/**
	 * Returns the Version DHL-Class
	 *
	 * @return StdClass - Version DHL-Class
	 */
	public function getVersionClass() {
		$class = new StdClass;

		$class->majorRelease = $this->getMayor();
		$class->minorRelease = $this->getMinor();

		return $class;
	}
}
