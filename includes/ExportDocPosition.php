<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 10.04.2017
 * Time: 12:48
 *
 * Notes: Contains the ExportDocPosition class
 */

use stdClass;

/**
 * Class ExportDocPosition
 *
 * @package Jahn\DHL
 *
 * Note: If min 1 value is filled out, all other values are required (else none is required)
 */
class ExportDocPosition {
	/**
	 * Description of the unit / position
	 *
	 * Min-Len: -
	 * Max-Len: 256
	 *
	 * @var string|null $itemDescription - Description of the unit / position
	 */
	private $itemDescription = null;

	/**
	 * Origin Country-ISO-Code
	 *
	 * Min-Len: 2
	 * Max-Len: 2
	 *
	 * @var string|null $countryOfOrigin - Origin Country-ISO-Code
	 */
	private $countryOfOrigin = null;

	/**
	 * Customs tariff number of the unit / position
	 *
	 * Min-Len: -
	 * Max-Len: 10
	 *
	 * @var string|null $hsCode - Customs tariff number of the unit / position (HS-code) or null for none
	 */
	private $hsCode = null;

	/**
	 * Quantity of the unit / position
	 *
	 * @var int|null $packagedQuantity - Quantity of the unit / position
	 */
	private $packagedQuantity = null;

	/**
	 * Net weight of the unit / position
	 *
	 * @var float|null $itemWeight - Net weight of the unit / position
	 */
	private $itemWeight = null;

	/**
	 * Customs value packagedQuantity of the unit / position
	 *
	 * @var float|null $itemValue - Customs value packagedQuantity of the unit / position
	 */
	private $itemValue = null;

	/**
	 * ExportDocPosition constructor.
	 *
	 * @param string $itemDescription - Description of the unit / position
	 * @param string $countryOfOrigin - Origin Country-ISO-Code
	 * @param string|null $hsCode - Customs tariff number of the unit / position (HS-code) or null for none
	 * @param int $packagedQuantity - Quantity of the unit / position
	 * @param int|float $itemWeight - Net weight of the unit / position
	 * @param int|float $itemWeightUom - Net weight of the unit / position
	 * @param int|float $itemValue - Customs value packagedQuantity of the unit / position
	 * @param int|float $itemValueCurrency - Customs value packagedQuantity of the unit / position
	 */
	public function __construct($itemDescription, $countryOfOrigin, $hsCode, $packagedQuantity, $itemWeight, $itemWeightUom, $itemValue, $itemValueCurrency) {
		if(! $itemDescription || ! $countryOfOrigin || ! $hsCode || ! $packagedQuantity || ! $itemWeight || ! $itemWeightUom || ! $itemValue || ! $itemValueCurrency) {
			trigger_error('PHP-DHL-API: ' . __CLASS__ . '->' . __FUNCTION__ .
				': All values must be filled out! (Not null, Not false, Not 0, Not "", Not empty) - Ignore this function for this call now', E_USER_WARNING);
			error_log('PHP-DHL-API: ' . __CLASS__ . '->' . __FUNCTION__ .
				': All values must be filled out! (Not null, Not false, Not 0, Not "", Not empty) - Ignore this function for this call now', E_USER_WARNING);
			return;
		}

		$this->setItemDescription($itemDescription);
		$this->setCountryOfOrigin($countryOfOrigin);
		$this->setHsCode($hsCode);
		$this->setPackagedQuantity($packagedQuantity);
		$this->setItemWeight((float) $itemWeight, $itemWeightUom);
		$this->setItemValue((float) $itemValue, $itemValueCurrency);
	}

	/**
	 * Get the Description
	 *
	 * @return string|null - Description or null on failure
	 */
	public function getItemDescription() {
		return $this->itemDescription;
	}

	/**
	 * Set the Description
	 *
	 * @param string $itemDescription - Description
	 */
	public function setItemDescription($itemDescription) {
		$this->itemDescription = substr($itemDescription,0,50);
	}

	/**
	 * Get the Country Code Origin
	 *
	 * @return string|null - Country Code Origin or null on failure
	 */
	public function getCountryOfOrigin() {
		return $this->countryOfOrigin;
	}

	/**
	 * Set the Country Code Origin
	 *
	 * @param string $countryOfOrigin - Country Code Origin
	 */
	public function setCountryOfOrigin($countryOfOrigin) {
		$this->countryOfOrigin = mb_strtoupper($countryOfOrigin);
	}

	/**
	 * Get the Custom Tariff Number
	 *
	 * @return float|int|string|null - Custom Tariff Number or null for none
	 */
	public function getHsCode() {
		return $this->hsCode;
	}

	/**
	 * Set the Custom Tariff Number
	 *
	 * @param float|int|string|null $hsCode - Custom Tariff Number or null for none
	 */
	public function setHsCode($hsCode) {
		$this->hsCode = $hsCode;
	}

	/**
	 * Get the PackagedQuantity
	 *
	 * @return int|null - PackagedQuantity or null on failure
	 */
	public function getPackagedQuantity() {
		return $this->packagedQuantity;
	}

	/**
	 * Set the PackagedQuantity
	 *
	 * @param int $packagedQuantity - PackagedQuantity
	 */
	public function setPackagedQuantity($packagedQuantity) {
		$this->packagedQuantity = $packagedQuantity;
	}

	/**
	 * Get the Weight in KG
	 *
	 * @return array|null - Weight in KG or null on failure
	 */
	public function getItemWeight() {
		return array(
			"value" => $this->itemWeight->value,
			"uom" => $this->itemWeight->uom
		);
	}

	/**
	 * Set the Weight in KG
	 *
	 * @param float $itemWeight - Weight in KG
	 */
	public function setItemWeight($itemWeight, $itemWeightUom) {
		$this->itemWeight=new stdclass;
		$this->itemWeight->value = $itemWeight;
		$this->itemWeight->uom = $itemWeightUom;
	}

	/**
	 * Get the Customs Value for the Unit / Package
	 *
	 * @return array|null - Custom Value for the Unit / Package or null on failure
	 */
	public function getItemValue() {
		return array(
			"value" => $this->itemValue->value,
			"currency" => $this->itemValue->currency
		);
	}

	/**
	 * Sets the Customs Value for the Unit / Package
	 *
	 * @param $itemValue - Customs Value for the Unit / Package
	 */
	public function setItemValue($itemValue, $itemValueCurrency) {
		$this->itemValue=new stdclass;
		$this->itemValue->value = $itemValue;
		$this->itemValue->currency = mb_strtoupper($itemValueCurrency);
	}

	/**
	 * Returns a Class for ExportDocPosition
	 *
	 * @return StdClass - DHL-ExportDocPosition-Class
	 * @since 2.0
	 */
	public function getExportDocPositionClass_v3() {
		$class = new StdClass;

		$class->itemDescription = $this->getItemDescription();
		$class->countryOfOrigin = $this->getCountryOfOrigin();
		$class->hsCode = $this->getHsCode();
		$class->packagedQuantity = $this->getPackagedQuantity();
		$class->itemWeight = $this->getItemWeight();
		$class->itemValue = $this->getItemValue();

		return $class;
	}

}
