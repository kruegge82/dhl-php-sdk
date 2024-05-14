<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 18.11.2016
 * Time: 13:07
 *
 * Notes: Details for a Shipment (Like size/Weight etc)
 */

/**
 * Class Shipments
 *
 * @package Jahn\DHL
 */
class Shipments {
	/**
	 * Product-Type Values:
	 *
	 * - Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE -> National-Package
	 * - Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE_PRIO -> National-Package-Prio
	 * - Shipments::PRODUCT_TYPE_INTERNATIONAL_PACKAGE -> International-Package
	 * - Shipments::PRODUCT_TYPE_EUROPA_PACKAGE -> Europa-Package
	 * - Shipments::PRODUCT_TYPE_PACKED_CONNECT -> Packed Connect
	 * - Shipments::PRODUCT_TYPE_SAME_DAY_PACKAGE -> Same-Day Package
	 * - Shipments::PRODUCT_TYPE_SAME_DAY_MESSENGER -> Same Day Messenger
	 * - Shipments::PRODUCT_TYPE_WISH_TIME_MESSENGER -> Wish Time Messenger
	 * - Shipments::PRODUCT_TYPE_AUSTRIA_PACKAGE -> Austria Package
	 * - Shipments::PRODUCT_TYPE_AUSTRIA_INTERNATIONAL_PACKAGE -> Austria International Package
	 * - Shipments::PRODUCT_TYPE_CONNECT_PACKAGE -> Connect Package
	 */
	const PRODUCT_TYPE_NATIONAL_PACKAGE = 'V01PAK';
	const PRODUCT_TYPE_NATIONAL_PACKAGE_PRIO = 'V01PRIO';
	const PRODUCT_TYPE_WARENPOST = 'V62WP';
	const PRODUCT_TYPE_WARENPOST_INT = 'V66WPI';
	const PRODUCT_TYPE_INTERNATIONAL_PACKAGE = 'V53WPAK';
	const PRODUCT_TYPE_EUROPA_PACKAGE = 'V54EPAK';
	const PRODUCT_TYPE_PACKED_CONNECT = 'V55PAK';
	const PRODUCT_TYPE_SAME_DAY_PACKAGE = 'V06PAK';
	const PRODUCT_TYPE_SAME_DAY_MESSENGER = 'V06TG';
	const PRODUCT_TYPE_WISH_TIME_MESSENGER = 'V06WZ';
	const PRODUCT_TYPE_AUSTRIA_PACKAGE = 'V86PARCEL';
	const PRODUCT_TYPE_AUSTRIA_INTERNATIONAL_PACKAGE = 'V82PARCEL';
	const PRODUCT_TYPE_CONNECT_PACKAGE = 'V87PARCEL';


	/**
	 * Shipments constructor.
	 *
	 * @param string $billingNumber - Account-Number
	 */
	public function __construct($billingNumber) {
		$this->setBillingNumber($billingNumber);
	}

	/**
	 * Contains which Product is used
	 *
	 * Allowed values: (Use PRODUCT_TYPE_* constants - See above)
	 * 	'V01PAK' or Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE -> National-Package
	 * 	'V01PRIO' or Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE_PRIO -> National-Package-Prio
	 * 	'V53WPAK' or Shipments::PRODUCT_TYPE_INTERNATIONAL_PACKAGE -> International-Package
	 * 	'V54EPAK' or Shipments::PRODUCT_TYPE_EUROPA_PACKAGE -> Europa-Package
	 * 	'V55PAK' or Shipments::PRODUCT_TYPE_PACKED_CONNECT -> Packed Connect
	 * 	'V06PAK' or Shipments::PRODUCT_TYPE_SAME_DAY_PACKAGE -> Same-Day Package
	 * 	'V06TG' or Shipments::PRODUCT_TYPE_SAME_DAY_MESSENGER -> Same Day Messenger
	 * 	'V06WZ' or Shipments::PRODUCT_TYPE_WISH_TIME_MESSENGER -> Wish Time Messenger
	 * 	'V86PARCEL' or Shipments::PRODUCT_TYPE_AUSTRIA_PACKAGE -> Austria Package
	 * 	'V82PARCEL' or Shipments::PRODUCT_TYPE_AUSTRIA_INTERNATIONAL_PACKAGE -> Austria International Package
	 * 	'V87PARCEL' or Shipments::PRODUCT_TYPE_CONNECT_PACKAGE -> Connect Package
	 *
	 * @var string $product - Product to use (Default: National Package)
	 */
	private string $product = self::PRODUCT_TYPE_NATIONAL_PACKAGE;

	/**
	 * Contains the Customer-Reference
	 *
	 * Note: Optional
	 *
	 * Min-Len: -
	 * Max-Len: 35
	 *
	 * @var string|null $refNo - Customer Reference or null for none
	 */
	private ?string $refNo = null;

	/**
	 * Name of a cost center
	 *
	 * Note: Optional
	 *
	 * Min-Len: -
	 * Max-Len: 50
	 *
	 * @var null|string $costCenter - Name of a Cost-Center or null for none
	 * @since 3.0
	 */
	private ?string $costCenter = null;

	/**
	 * Contains the Return-Account-Number (EKP)
	 *
	 * Note: Optional
	 *
	 * Min-Len: 14
	 * Max-Len: 14
	 *
	 * @var string|null $creationSoftware - creationSoftware or null for none
	 */
	private ?string $creationSoftware = null;

	/**
	 * Contains the Shipment-Date
	 *
	 * Note: ISO-Date-Format (YYYY-MM-DD)
	 *
	 * Min-Len: 10
	 * Max-Len: 10
	 *
	 * @var string|null $shipDate - Shipment-Date or null for today (+1 Day if Sunday)
	 */
	private ?string $shipDate = null;
	private ?Shipper $shipper = null;
	private ?Consignee $consignee = null;
	/**
	 * Contains the Shipment-Date
	 *
	 * Note: ISO-Date-Format (YYYY-MM-DD)
	 *
	 * Min-Len: 10
	 * Max-Len: 10
	 *
	 * @var Details|null $details - Service Object | null for none
	 */
	private ?Details $details = null;

	/**
	 * Contains the Service Object (Many settings for the Shipment)
	 *
	 * Note: Optional
	 *
	 * @var Services|null $services - Service Object | null for none
	 */
	private ?Services $services = null;


	/**
	 * Contains the Export-Document-Settings Object
	 *
	 * Note: Optional
	 *
	 * @var Customs|null $customs - Export-Document-Settings Object | null for none
	 */
	private ?Customs $customs = null;

	public ?string $billingNumber = null;

	/**
	 * Get which Product is used
	 *
	 * 	Return values: (Use PRODUCT_TYPE_* constants - See above)
	 * 	'V01PAK' or Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE -> National-Package
	 * 	'V01PRIO' or Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE_PRIO -> National-Package-Prio
	 * 	'V53WPAK' or Shipments::PRODUCT_TYPE_INTERNATIONAL_PACKAGE -> International-Package
	 * 	'V54EPAK' or Shipments::PRODUCT_TYPE_EUROPA_PACKAGE -> Europa-Package
	 * 	'V55PAK' or Shipments::PRODUCT_TYPE_PACKED_CONNECT -> Packed Connect
	 * 	'V06PAK' or Shipments::PRODUCT_TYPE_SAME_DAY_PACKAGE -> Same-Day Package
	 * 	'V06TG' or Shipments::PRODUCT_TYPE_SAME_DAY_MESSENGER -> Same Day Messenger
	 * 	'V06WZ' or Shipments::PRODUCT_TYPE_WISH_TIME_MESSENGER -> Wish Time Messenger
	 * 	'V86PARCEL' or Shipments::PRODUCT_TYPE_AUSTRIA_PACKAGE -> Austria Package
	 * 	'V82PARCEL' or Shipments::PRODUCT_TYPE_AUSTRIA_INTERNATIONAL_PACKAGE -> Austria International Package
	 * 	'V87PARCEL' or Shipments::PRODUCT_TYPE_CONNECT_PACKAGE -> Connect Package
	 *
	 * @return string - Used Product
	 */
	public function getProduct(): string
	{
		return $this->product;
	}

	/**
	 * Set which Product is used
	 *
	 * Allowed values: (Use PRODUCT_TYPE_* constants - See above)
	 * 	'V01PAK' or Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE -> National-Package
	 * 	'V01PRIO' or Shipments::PRODUCT_TYPE_NATIONAL_PACKAGE_PRIO -> National-Package-Prio
	 * 	'V53WPAK' or Shipments::PRODUCT_TYPE_INTERNATIONAL_PACKAGE -> International-Package
	 * 	'V54EPAK' or Shipments::PRODUCT_TYPE_EUROPA_PACKAGE -> Europa-Package
	 * 	'V55PAK' or Shipments::PRODUCT_TYPE_PACKED_CONNECT -> Packed Connect
	 * 	'V06PAK' or Shipments::PRODUCT_TYPE_SAME_DAY_PACKAGE -> Same-Day Package
	 * 	'V06TG' or Shipments::PRODUCT_TYPE_SAME_DAY_MESSENGER -> Same Day Messenger
	 * 	'V06WZ' or Shipments::PRODUCT_TYPE_WISH_TIME_MESSENGER -> Wish Time Messenger
	 * 	'V86PARCEL' or Shipments::PRODUCT_TYPE_AUSTRIA_PACKAGE -> Austria Package
	 * 	'V82PARCEL' or Shipments::PRODUCT_TYPE_AUSTRIA_INTERNATIONAL_PACKAGE -> Austria International Package
	 * 	'V87PARCEL' or Shipments::PRODUCT_TYPE_CONNECT_PACKAGE -> Connect Package
	 *
	 * @param string $product - Product, which should be used
	 */
	public function setProduct(string $product): void
	{
		$this->product = $product;
	}


	/**
	 * Get the Customer-Reference
	 *
	 * @return null|string - Customer Reference or null for none
	 */
	public function getRefNo(): ?string
	{
		return $this->refNo;
	}

	/**
	 * Set the Customer-Reference
	 *
	 * @param string|null $refNo - Customer Reference or null for none
	 */
	public function setRefNo(?string $refNo): void
	{
		$this->refNo = $refNo;
	}

	/**
	 * Get the Shipment-Date (and set the default one -today- if none was set)
	 *
	 * @return string - Shipment-Date as ISO-Date String YYYY-MM-DD
	 */
	public function getShipDate()
	{
		if($this->shipDate === null)
			$this->setShipDate($this->createDefaultShipmentDate());

		return $this->shipDate;
	}

	/**
	 * Set the Shipment-Date
	 *
	 * @param string|int|null $shipDate - Shipment-Date as String YYYY-MM-DD or the int value time() of the date | null for today (+1 Day on Sunday)
	 * @param bool $useIntTime - Use the int Time Value instead of a String
	 */
	public function setShipDate($shipDate, bool $useIntTime = false) {
		if($useIntTime) {
			// Convert Time-Stamp to Date
			$shipDate = date('Y-m-d', $shipDate);

			if($shipDate === false)
				$shipDate = null;
		}

		$this->shipDate = $shipDate;
	}

	/**
	 * get the name of a Cost center
	 *
	 * @return string|null - Name of a Cost center or null for none
	 * @since 3.0
	 */
	public function getCostCenter(): ?string {
		return $this->costCenter;
	}

	/**
	 * Set the Name of a Cost center or null for none
	 *
	 * @param string|null $costCenter - Name of a Cost center or null for none
	 * @since 3.0
	 */
	public function setCostCenter(?string $costCenter): void {
		$this->costCenter = $costCenter;
	}

	/**
	 * Get the Service-Object
	 *
	 * @return Services|null - Service-Object or null if none
	 */
	public function getServices(): ?Services
	{
		return $this->services;
	}

	/**
	 * Set the Service-Object
	 *
	 * @param Services|null $services - Service-Object or null for none
	 */
	public function setServices(?Services $services): void
	{
		$this->services = $services;
	}


	/**
	 * Get the Customs-Object
	 *
	 * @return Customs|null - Customs-Object or null if none
	 */
	public function getCustoms(): ?Customs
	{
		return $this->customs;
	}

	/**
	 * Set the Customs-Object
	 *
	 * @param Customs|null $customs - Customs-Object or null for none
	 */
	public function setCustoms(?Customs $customs): void
	{
		$this->customs = $customs;
	}

	/**
	 * Creates a Default Shipment-Date (Today or if Sunday the next Day)
	 *
	 * @return string - Default-Date as ISO-Date String
	 */
	public function createDefaultShipmentDate() {
		$now = time();
		$weekDay = date('w', $now);

		if($weekDay == 0)
			$now += 86400; // Increase Day by 1 if Sunday

		return date('Y-m-d', $now);
	}


	/**
	 * @return string|null
	 */
	public function getCreationSoftware(): ?string
	{
		return $this->creationSoftware;
	}

	/**
	 * @param string|null $creationSoftware
	 */
	public function setCreationSoftware(?string $creationSoftware): void
	{
		$this->creationSoftware = $creationSoftware;
	}

	/**
	 * @return Shipper
	 */
	public function getShipper(): ?Shipper
	{
		return $this->shipper;
	}

	/**
	 * @param Shipper $shipper
	 */
	public function setShipper(Shipper $shipper): void
	{
		$this->shipper = $shipper;
	}

	/**
	 * @return Consignee
	 */
	public function getConsignee(): ?Consignee
	{
		return $this->consignee;
	}

	/**
	 * @param Consignee $consignee
	 */
	public function setConsignee(Consignee $consignee): void
	{
		$this->consignee = $consignee;
	}

	/**
	 * @return Details|null
	 */
	public function getDetails(): ?Details
	{
		return $this->details;
	}

	/**
	 * @param Details|null $details
	 */
	public function setDetails(?Details $details): void
	{
		$this->details = $details;
	}
	/**
	 * @return string|null
	 */
	public function getBillingNumber(): ?string
	{
		return $this->billingNumber;
	}

	/**
	 * @param string|null $billingNumber
	 */
	public function setBillingNumber(?string $billingNumber): void
	{
		$this->billingNumber = $billingNumber;
	}


	/**
	 * Returns an DHL-Class of this Object for DHL-Shipment Details
	 *
	 * @return array - DHL-Shipments-Class
	 * @throws \Exception
	 * @since 3.0
	 */
	public function getShipmentsClass_v3(): array
	{
		$class = array();

		$class['product'] = $this->getProduct();
		$class['billingNumber'] = $this->getBillingNumber();

		if($this->getRefNo() !== null)
			$class['refNo'] = $this->getRefNo();
		if($this->getCostCenter() !== null)
			$class['costCenter'] = $this->getCostCenter();

		$class['shipDate'] = $this->getShipDate();
		if($this->getCreationSoftware() !== null)
			$class['creationSoftware'] = $this->getCreationSoftware();
		$class['shipper'] = $this->getShipper()->getClass_v3();

		$class['consignee'] = $this->getConsignee()->getClass_v3();

		$class['details'] = $this->getDetails()->getClass_V3();
		if($this->getServices()!==null)
			$class['services'] = $this->getServices()->getClass_V3();
		if($this->getCustoms() !== null)
			$class['customs'] = $this->getCustoms()->getClass_V3();
		return $class;
	}

}
