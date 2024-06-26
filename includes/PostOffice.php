<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 20.03.2017
 * Time: 13:23
 *
 * Notes: Contains the  PostOffice Class
 */

use stdClass;

/**
 * Class  PostOffice
 *
 * @package Jahn\DHL
 */
class  PostOffice extends Consignee {
	/**
	 * Contains the Post-Number
	 *
	 * Min-Len: 1
	 * Max-Len: 10
	 *
	 * @var string $postNumber - Post-Number
	 */
	private $postNumber = '';

	/**
	 * Contains the Post-Number
	 *
	 * Min-Len: 1
	 * Max-Len: 10
	 *
	 * @var string $name - Post-Number
	 */
	private $name = '';

	/**
	 * Contains the Post- PostOffice-Number
	 *
	 * Min-Len: 3
	 * Max-Len: 3
	 *
	 * @var string $retailID - Post- PostOffice-Number
	 */
	private $retailID = '';

	/**
	 * Get the Post-Number
	 *
	 * @return string - Post-Number
	 */
	public function getPostNumber() {
		return $this->postNumber;
	}

	/**
	 * Set the Post-Number
	 *
	 * @param string $postNumber - Post-Number
	 */
	public function setPostNumber($postNumber) {
		$this->postNumber = $postNumber;
	}

	/**
	 * Get the  PostOffice-Number
	 *
	 * @return string -  PostOffice-Number
	 */
	public function getRetailID() {
		return $this->retailID;
	}

	/**
	 * Set the  PostOffice-Number
	 *
	 * @param string $retailID -  PostOffice-Number
	 */
	public function setRetailID($retailID) {
		$this->retailID = $retailID;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * Returns a Class for the DHL-SendPerson
	 *
	 * @return StdClass - DHL-SendPerson-class
	 * @since 2.0
	 */
	public function getClass_v3(): stdClass
	{
		$class = new StdClass;

		$class->name = $this->getName();
		$class->email = $this->getEmail();
		$class->retailID = $this->getRetailID();
		$class->postNumber = $this->getPostNumber();
		$class->postalCode = $this->getPostalCode();
		$class->city = $this->getCity();
		$class->country = $this->getCountry();

		return $class;
	}

}
