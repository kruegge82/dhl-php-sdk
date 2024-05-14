<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 28.01.2017
 * Time: 19:15
 *
 * Notes: Contains the Sender Class
 */

use stdClass;

/**
 * Class Sender
 *
 * @package Jahn\DHL
 */
class Shipper extends SendPerson {

	/**
	 * Returns a Class for the DHL-SendPerson
	 *
	 * @return StdClass - DHL-SendPerson-class
	 * @since 3.0
	 */
	public function getClass_v3(): stdClass
	{
		$class = new StdClass;

		// Set Name
		$class->name1 = $this->getName1();
		if($this->getName2() !== null)
			$class->name2 = $this->getName2();
		if($this->getName3() !== null)
			$class->name3 = $this->getName3();

		// Address
		$class->addressStreet = $this->getAddressStreet();
		$class->addressHouse = $this->getAddressHouse();
		$class->postalCode = $this->getPostalCode();
		$class->city = $this->getCity();
		$class->country = $this->getCountry();

		if($this->getContactName() !== null)
			$class->contactName = $this->getContactName();
		if($this->getEmail() !== null)
			$class->email = $this->getEmail();
		if($this->getPhone() !== null)
			$class->phone = $this->getPhone();

		if($this->getShipperRef() !== null)
			$class->shipperRef = $this->getShipperRef();

		return $class;
	}
}
