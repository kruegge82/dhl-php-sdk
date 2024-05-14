<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 26.01.2017
 * Time: 18:18
 *
 * Notes: Contains the Service Class
 */

use stdClass;

/**
 * Class Service
 *
 * @package Jahn\DHL
 */
class Services {

	/**
	 * Contains the details of the preferred Neighbour (Free text)
	 *
	 * Note: Optional
	 *
	 * Min-Len: 1
	 * Max-Len: 100
	 *
	 * @var string|null $preferredNeighbour - Preferred neighbour. Can be specified as text
	 */
	private ?string $preferredNeighbour = null;

	/**
	 * Contains the Type for the "Endorsement"-Service
	 *
	 * Note: Optional|Required if $endorsementEnabled
	 *
	 * Values for national:
	 * 	'SOZU': (Return immediately),
	 * 	'ZWZU': (2nd attempt of Delivery);
	 * -----------------------------
	 * Values for International:
	 * 	'IMMEDIATE': (Sending back immediately to sender),
	 * 	'AFTER_DEADLINE': (Sending back immediately to sender after expiration of time),
	 * 	'ABANDONMENT': (Abandonment of parcel at the hands of sender (free of charge))
	 *
	 * @var string|null $endorsement - Endorsement-Service Type | null for none
	 */

	private ?string $endorsement = null;

	/**
	 * Note: Special instructions for delivery. 2 character code, possible values agreed in contract.
	 * pattern: [a-zA-Z0-9]{2}
	 * example: ZZ
	*/
	private ?string $individualSenderRequirement = null;
	private ?bool $closestDroppoint = null;
	private $dhlRetoure = null;
	private ?bool $postalDeliveryDutyPaid = null;
	private ?float $value = null;
	private ?string $currency = null;
	private ?bool $signedForByRecipient = null;

	/**
	 * Contains the Age that the Receiver should be at least have
	 *
	 * Note: Optional|Required if $visualCheckOfAge
	 *
	 * Min-Len: 3
	 * Max-Len: 3
	 *
	 * The following Values are allowed:
	 * 	'A16': Person must be 16+
	 * 	'A18': Person must be 18+
	 *
	 * @var string|null $visualCheckOfAge - Minimum-Age of the Receiver | null for none
	 */
	private ?string $visualCheckOfAge = null;

	/**
	 * Contains the Age that the Receiver should be at least have
	 *
	 * Note: Optional|Required if $visualCheckOfAgeEnabled
	 *
	 * Min-Len: 3
	 * Max-Len: 3
	 *
	 * The following Values are allowed:
	 * 	'A16': Person must be 16+
	 * 	'A18': Person must be 18+
	 *
	 * @var string|null $visualCheckOfAge - Minimum-Age of the Receiver | null for none
	 */
	private ?string $shippingConfirmation = null;

	/**
	 * Contains details of the preferred Location (Free text)
	 *
	 * Min-Len: 1
	 * Max-Len: 100
	 *
	 * @var string|null $preferredLocation - Details of the preferred Location (Free text) | null for none
	 */
	private ?string $preferredLocation = null;



	/**
	 * Contains the details of the preferred Day (Free text)
	 *
	 * Note: Optional|Required if $preferredDayEnabled
	 *
	 * Preferred day of delivery in format YYYY-MM-DD. Shipper can request a preferred day of delivery. The preferred day should be between 2 and 6 working days after handover to DHL.
	 *
	 * Min-Len: 1
	 * Max-Len: 100
	 *
	 * @var string|null $preferredDay - Details of the preferred Day (Free text) | null for none
	 */
	private ?string $preferredDay = null;

	/**
	 * Contains if Neighbour delivery is disabled
	 *
	 * Note: Optional
	 *
	 * @var bool|null $noNeighbourDelivery - Is this enabled | null uses default
	 */
	private ?bool $noNeighbourDelivery = null;

	/**
	 * Contains if named Person can only accept delivery
	 *
	 * Note: Optional
	 *
	 * @var bool|null $namedPersonOnly - Is this enabled | null uses default
	 */
	private ?bool $namedPersonOnly = null;

	/**
	 * Contains if Premium is enabled (for fast and safe delivery of international shipments)
	 *
	 * Note: Optional
	 *
	 * @var bool|null $premium - Is this enabled | null uses default
	 */
	private ?bool $premium = null;

	/**
	 * Contains if Cash on delivery (COD) is enabled
	 *
	 * Note: Optional
	 *
	 * @var bankData|null $cashOnDelivery - Is this enabled | null uses default
	 */
	private ?bankData $cashOnDelivery = null;

	/**
	 * Contains if you deliver Bulky-Goods
	 *
	 * Note: Optional
	 *
	 * @var bool|null $bulkyGoods - Is this enabled | null uses default
	 */
	private ?bool $bulkyGoods = null;


	/**
	 * Contains the Ident-Check Object
	 *
	 * Note: Optional|Required if $indentCheckEnabled
	 *
	 * @var IdentCheck|null $identCheck - Ident-Check Object | null for none
	 */
	private ?IdentCheck $identCheck = null;

	/**
	 * Contains the details for ParcelOutletRouting
	 *
	 * Note: Optional
	 * Undeliverable domestic shipment can be forwarded and held at retail. Notification to email (fallback: consignee email) will be used.
	 *
	 * Min-Len: 1
	 * Max-Len: 100
	 *
	 * @var string|null $parcelOutletRouting - Details of the ParcelOutletRouting | null for none
	 * @since 3.0
	 */
	private ?string $parcelOutletRouting = null;

	/**
	 * Get the Date for the Service "DayOfDelivery"
	 *
	 * @return null|string - The day of Delivery as ISO-Date-Format (YYYY-MM-DD) or null for none
	 */
	public function getPreferredDay(): ?string
	{
		return $this->preferredDay;
	}

	/**
	 * Set the Date for the Service "DayOfDelivery"
	 *
	 * @param null|string|int $preferredDay - The Day of Delivery as ISO-Date-Format (YYYY-MM-DD), the day as time() int value or null for none
	 * @param bool $useIntTime - Use the int Time Value instead of a String
	 */
	public function setPreferredDay(string $preferredDay, bool $useIntTime = false): void
	{
		if($useIntTime) {
			$preferredDay = date('Y-m-d', $preferredDay);

			if($preferredDay === false)
				$preferredDay = null;
		}

		$this->preferredDay = $preferredDay;
	}


	/**
	 * Get the Endorsement Type
	 *
	 * Values for national:
	 * 	'RETURN': (Sending back immediately to sender),
	 * 	'ABANDON': (Abandonment of parcel at the hands of sender (free of charge))
	 * ---------------------------
	 * Values for International:
	 * 	'RETURN': (Sending back immediately to sender),
	 * 	'ABANDON': (Abandonment of parcel at the hands of sender (free of charge))
	 *
	 * @return null|string - Endorsement-Service Type or null for none
	 */
	public function getEndorsement(): ?string
	{
		return $this->endorsement;
	}

	/**
	 * Set the Endorsement Type
	 *
	 * Values for national:
	 * 	'RETURN': (Sending back immediately to sender),
	 * 	'ABANDON': (Abandonment of parcel at the hands of sender (free of charge))
	 * ---------------------------
	 * Values for International:
	 * 	'RETURN': (Sending back immediately to sender),
	 * 	'ABANDON': (Abandonment of parcel at the hands of sender (free of charge))
	 *
	 * @param string|null $endorsement - Endorsement-Service Type or null for none
	 */
	public function setEndorsement(?string $endorsement): void
	{
		$this->endorsement = $endorsement;
	}


	/**
	 * Get the Age that the Receiver should be at least have
	 *
	 * You will get the following values:
	 * 	'A16': Person must be 16+
	 * 	'A18': Person must be 18+
	 *
	 * @return null|string - Minimum-Age of the Receiver or null for none
	 */
	public function getVisualCheckOfAge(): ?string
	{
		return $this->visualCheckOfAge;
	}

	/**
	 * Set the Age that the Receiver should be at least have
	 *
	 * The following Values are allowed:
	 * 	'A16': Person must be 16+
	 * 	'A18': Person must be 18+
	 *
	 * @param string|null $visualCheckOfAge - Minimum-Age of the Receiver or null for none
	 */
	public function setVisualCheckOfAge(?string $visualCheckOfAge): void
	{
		$this->visualCheckOfAge = $visualCheckOfAge;
	}

	/**
	 * Get the details of the preferred Location (Free text)
	 *
	 * @return null|string - Details of the preferred Location (Free text) or null for none
	 */
	public function getPreferredLocation(): ?string
	{
		return $this->preferredLocation;
	}

	/**
	 * Set the details of the preferred Location (Free text)
	 *
	 * @param string|null $preferredLocation - Details of the preferred Location (Free text) or null for none
	 */
	public function setPreferredLocation(?string $preferredLocation): void
	{
		$this->preferredLocation = $preferredLocation;
	}


	/**
	 * Get the details of the preferred Neighbour (Free text)
	 *
	 * @return null|string - The details of the preferred Neighbour (Free text) or null for none
	 */
	public function getPreferredNeighbour(): ?string
	{
		return $this->preferredNeighbour;
	}

	/**
	 * Set the details of the preferred Neighbour (Free text)
	 *
	 * @param string|null $preferredNeighbour - Preferred neighbour. Can be specified as text
	 */
	public function setPreferredNeighbour(?string $preferredNeighbour): void
	{
		$this->preferredNeighbour = $preferredNeighbour;
	}

	/**
	 * Get if the Service "noNeighbourDelivery" is enabled
	 *
	 * @return bool|null - Is the Service "noNeighbourDelivery" enabled or null for default
	 */
	public function getNoNeighbourDelivery(): ?bool
	{
		return $this->noNeighbourDelivery;
	}

	/**
	 * Set if the Service "noNeighbourDelivery" is enabled
	 *
	 * @param bool|null $noNeighbourDelivery - Is the Service "DisableNeighbourDelivery" enabled or null for default
	 */
	public function setNoNeighbourDelivery(?bool $noNeighbourDelivery): void
	{
		$this->noNeighbourDelivery = $noNeighbourDelivery;
	}

	/**
	 * Get if named Person can only accept delivery
	 *
	 * @return bool|null - Named Person can only accept delivery or null for default
	 */
	public function getNamedPersonOnly(): ?bool
	{
		return $this->namedPersonOnly;
	}

	/**
	 * Set if named Person can only accept delivery
	 *
	 * @param bool|null $namedPersonOnly - Named Person can only accept delivery or null for default
	 */
	public function setNamedPersonOnly(?bool $namedPersonOnly): void
	{
		$this->namedPersonOnly = $namedPersonOnly;
	}

	/**
	 * Get if Premium is enabled (for fast and safe delivery of international shipments)
	 *
	 * @return bool|null - Premium is enabled or null for default
	 */
	public function getPremium(): ?bool
	{
		return $this->premium;
	}

	/**
	 * Set if Premium is enabled (for fast and safe delivery of international shipments)
	 *
	 * @param bool|null $premium - Premium is enabled or null for default
	 */
	public function setPremium(?bool $premium): void
	{
		$this->premium = $premium;
	}

	/**
	 * Get if Cash on delivery (COD) is enabled
	 *
	 * @return BankData|null - Is Cash on delivery (COD) enabled or null for default
	 */
	public function getCashOnDelivery(): ?BankData
	{
		return $this->cashOnDelivery;
	}

	/**
	 * Set if Cash on delivery (COD) is enabled
	 *
	 * @param bankdata|null $cashOnDelivery - Is Cash on delivery (COD) enabled or null for default
	 */
	public function setCashOnDelivery(?BankData $cashOnDelivery):void {
		$this->cashOnDelivery = $cashOnDelivery;
	}

	/**
	 * Get the Amount with that the Shipment is insured
	 *
	 * @return stdClass|null - The Amount with that the Shipment is insured or null for none
	 */
	public function getAdditionalInsurance(): ?stdClass
	{
		$class = new stdclass;
		$class->currency = $this->currency;
		$class->value= $this->value;
		return $class;
	}

	/**
	 * Set the Amount with that the Shipment is insured
	 *
	 * @param float|null $value - The Amount with that the Shipment is insured or null for none
	 * @param string|null $currency - The Amount with that the Shipment is insured or null for none
	 */
	public function setAdditionalInsurance(?float $value, ?string $currency): void
	{
		$this->value = $value;
		$this->currency = mb_strtoupper($currency);
	}

	/**
	 * Get if you deliver Bulky-Goods
	 *
	 * @return bool|null - Do you deliver Bulky-Goods or null for default
	 */
	public function getBulkyGoods(): ?bool
	{
		return $this->bulkyGoods;
	}

	/**
	 * Set if you deliver Bulky-Goods
	 *
	 * @param bool|null $bulkyGoods - Do you deliver Bulky-Goods or null for default
	 */
	public function setBulkyGoods(?bool $bulkyGoods): void
	{
		$this->bulkyGoods = $bulkyGoods;
	}

	/**
	 * Get the IdentCheck Object
	 *
	 * @return IdentCheck|null - The IdentCheck Object or null for none
	 */
	public function getIdentCheck(): ?IdentCheck
	{
		return $this->identCheck;
	}

	/**
	 * Set the IdentCheck Object
	 *
	 * @param IdentCheck|null $identCheck - The IdentCheck Object or null for none
	 */
	public function setIdentCheck(?identCheck $identCheck): void
	{
		$this->identCheck=$identCheck;
	}

	/**
	 * Get the ParcelOutletRouting details
	 *
	 * @return string|null - ParcelOutletRouting details or null for none
	 * @since 3.0
	 */
	public function getParcelOutletRouting(): ?string {
		return $this->parcelOutletRouting;
	}

	/**
	 * Set the ParcelOutletRouting details
	 *
	 * @param string|null $parcelOutletRouting - ParcelOutletRouting details or null for none
	 * @since 3.0
	 */
	public function setParcelOutletRouting(?string $parcelOutletRouting): void {
		$this->parcelOutletRouting = $parcelOutletRouting;
	}

	/**
	 * Get the PostalDeliveryDutyPaid details
	 *
	 * @return bool|null - PostalDeliveryDutyPaid details or null for none
	 * @since 3.0
	 */
	public function getPostalDeliveryDutyPaid(): ?bool {
		return $this->postalDeliveryDutyPaid;
	}

	/**
	 * Set the PostalDeliveryDutyPaid details
	 *
	 * @param bool|null $postalDeliveryDutyPaid - PostalDeliveryDutyPaid details or null for none
	 * @since 3.0
	 */
	public function setPostalDeliveryDutyPaid(?bool $postalDeliveryDutyPaid): void {
		$this->postalDeliveryDutyPaid = $postalDeliveryDutyPaid;
	}

	/**
	 * Get the individualSenderRequirement details
	 *
	 * @return string|null - individualSenderRequirement details or null for none
	 * @since 3.0
	 */
	public function getIndividualSenderRequirement(): ?string {
		return $this->individualSenderRequirement;
	}

	/**
	 * Set the individualSenderRequirement details
	 *
	 * @param string|null $individualSenderRequirement - individualSenderRequirement details or null for none
	 * @since 3.0
	 */
	public function setIndividualSenderRequirement(?string $individualSenderRequirement): void {
		$this->individualSenderRequirement = $individualSenderRequirement;
	}

	/**
	 * Get the closestDroppoint details
	 *
	 * @return bool|null - closestDroppoint details or null for none
	 * @since 3.0
	 */
	public function getClosestDroppoint(): ?bool {
		return $this->closestDroppoint;
	}

	/**
	 * Set the closestDroppoint details
	 *
	 * @param bool|null $closestDroppoint - closestDroppoint details or null for none
	 * @since 3.0
	 */
	public function setClosestDroppoint(?bool $closestDroppoint): void {
		$this->closestDroppoint = $closestDroppoint;
	}

	/**
	 * Get the shippingConfirmation details
	 *
	 * @return string|null - Email address(es) of the recipient of the confirmation.
	 * @since 3.0
	 */
	public function getShippingConfirmation(): ?string {
		return $this->shippingConfirmation;
	}

	/**
	 * Set the shippingConfirmation details
	 *
	 * @param string|null $shippingConfirmation - shippingConfirmation details or null for none
	 * @since 3.0
	 */
	public function setShippingConfirmation(?string $shippingConfirmation): void {
		$this->shippingConfirmation = $shippingConfirmation;
	}

	/**
	 * @return null
	 */
	public function getDhlRetoure()
	{
		return $this->dhlRetoure;
	}

	/**
	 * @param null $dhlRetoure
	 */
	public function setDhlRetoure($dhlRetoure): void
	{
		$this->dhlRetoure = $dhlRetoure;
	}

	/**
	 * @return bool|null
	 */
	public function getSignedForByRecipient(): ?bool
	{
		return $this->signedForByRecipient;
	}

	/**
	 * @param bool|null $signedForByRecipient
	 */
	public function setSignedForByRecipient(?bool $signedForByRecipient): void
	{
		$this->signedForByRecipient = $signedForByRecipient;
	}

	/**
	 * Get the Class of this Service-Object
	 *
	 * @return StdClass - Service-DHL-Class
	 * @since 2.0
	 */
	public function getClass_V3(): stdClass
	{
		$class = new StdClass;

		if($this->getPreferredNeighbour() !== null)
			$class->preferredNeighbour = $this->getPreferredNeighbour();
		if($this->getPreferredLocation() !== null)
			$class->preferredLocation = $this->getPreferredLocation();
		if($this->getShippingConfirmation() !== null)
			$class->shippingConfirmation->email = $this->getShippingConfirmation();
		if($this->getVisualCheckOfAge() !== null)
			$class->visualCheckOfAge = $this->getVisualCheckOfAge();
		if($this->getNamedPersonOnly() !== null)
			$class->namedPersonOnly = $this->getNamedPersonOnly();
		if($this->getIdentCheck()!=null)
			$class->identCheck = $this->getIdentCheck()->getIdentClass_v3();
		if($this->getEndorsement() !== null)
			$class->endorsement = $this->getEndorsement();
		if($this->getPreferredDay() !== null)
			$class->preferredDay = $this->getPreferredDay();
		if($this->getNoNeighbourDelivery() !== null)
			$class->noNeighbourDelivery = $this->getNoNeighbourDelivery();
		if($this->getAdditionalInsurance()->value !== null)
			$class->additionalInsurance = $this->getAdditionalInsurance();
		if($this->getBulkyGoods() !== null)
			$class->bulkyGoods = $this->getBulkyGoods();
		if($this->getCashOnDelivery() !== null)
			$class->cashOnDelivery = $this->getCashOnDelivery()->getBankClass_v3();
		if($this->getIndividualSenderRequirement() !== null)
			$class->individualSenderRequirement = $this->getIndividualSenderRequirement();
		if($this->getPremium() !== null)
			$class->premium = $this->getPremium();
		if($this->getClosestDroppoint() !== null)
			$class->closestDropPoint = $this->getClosestDroppoint();
		if($this->getParcelOutletRouting() !== null)
			$class->parcelOutletRouting = $this->getParcelOutletRouting();
		if($this->getDhlRetoure() !== null)
			$class->dhlRetoure = $this->getDhlRetoure()->getClass_v3();
		if($this->getPostalDeliveryDutyPaid() !== null)
			$class->postalDeliveryDutyPaid = $this->getPostalDeliveryDutyPaid();
		if($this->getSignedForByRecipient()!== null)
			$class->signedForReceipient = $this->getSignedForByRecipient();

		return $class;
	}

}
