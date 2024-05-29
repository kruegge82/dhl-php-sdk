<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 02.09.2018
 * Time: 13:13
 *
 * Notes: Contains the LabelData Class
 */

/**
 * Class LabelData
 *
 * @package Jahn\DHL
 */
class LabelData extends Version implements LabelResponse {
	/**
	 * Contains the Status-Code
	 *
	 * - Response::DHL_ERROR_NOT_SET (-1) -> Status-Code was not set
	 * - Response::DHL_ERROR_NO_ERROR (0) -> No Error occurred
	 * - Response::DHL_ERROR_WEAK_WARNING (1) -> A week warning has occurred
	 * - Response::DHL_ERROR_SERVICE_TMP_NOT_AVAILABLE (500) -> DHL-API Service is not available
	 * - Response::DHL_ERROR_GENERAL (1000)-> General Error
	 * - Response::DHL_ERROR_AUTH_FAILED (1001) -> Authentication has failed
	 * - Response::DHL_ERROR_HARD_VAL_ERROR (1101) -> A hard-validation Error has occurred
	 * - Response::DHL_ERROR_UNKNOWN_SHIPMENT_NUMBER (2000) -> Given Shipment-Number is unknown
	 *
	 * @var int $statusCode - Status-Code
	 */
	private $statusCode = Response::DHL_ERROR_NOT_SET;

	/**
	 * Contains the Status-Text
	 *
	 * @var string|null $statusText - Status-Text | null if not set
	 */
	private $statusText = null;

	/**
	 * Contains the Status-Message (Mostly more detailed, but longer)
	 *
	 * @var string|null $statusMessage - Status-Message | null if not set
	 */
	private $statusMessage = null;

	/**
	 * Shipment-Number
	 *
	 * @var null|string $shipmentNumber - Shipment-Number | null if not set
	 */
	private $shipmentNumber = null;

	/**
	 * RoutingCode
	 *
	 * @var null|string $routingCode - routingCode | null if not set
	 */
	private $routingCode = null;

	/**
	 * Return Shipment-Number
	 *
	 * @var null|string $returnShipmentNumber - Return Shipment-Number | null if not set
	 */
	private $returnShipmentNumber = null;

	/**
	 * Label URL/Base64-Data - Can also have the return label in one
	 *
	 * @var null|string $label - Label-URL or Base64-Label-Data | null if not set
	 */
	private $label = null;

	/**
	 * Return Label URL/Base64-Data
	 *
	 * @var null|string $returnLabel - Return Label-URL/Base64-Label-Data or null if not requested
	 */
	private $returnLabel = null;

	/**
	 * Export-Document-Label-URL/Base64-Data
	 *
	 * @var null|string $exportDoc - Export-Document Label-URL/Base64-Label-Data or null if not requested
	 */
	private $exportDoc = null;

	/**
	 * Cod-Label-URL/Base64-Data
	 *
	 * @var null|string $codLabel - Cod-Label-URL/Base64-Data or null if not requested
	 */
	private $codLabel = null;

	private $labelFormat = null;
	private $returnLabelFormat = null;
	private $customsDocFormat = null;
	private $codLabelFormat = null;

	private $shipmentRefNo = null;

	private $token = null;
	/**
	 * LabelData constructor.
	 *
	 * @param string $version - Current DHL-Version
	 * @param object $labelData - LabelData-Object from DHL-Response
	 */
	public function __construct($version, $labelData) {
		parent::__construct($version);

		if($labelData !== null) {
			switch($this->getMayor()) {
				case 1:
					break;
				case 2:
				default:
					$this->loadLabelData_v3($labelData);
			}
		}
	}

	/**
	 * Getter for Status-Code
	 *
	 * - Response::DHL_ERROR_NOT_SET (-1) -> Status-Code was not set
	 * - Response::DHL_ERROR_NO_ERROR (0) -> No Error occurred
	 * - Response::DHL_ERROR_WEAK_WARNING (1) -> A week warning has occurred
	 * - Response::DHL_ERROR_SERVICE_TMP_NOT_AVAILABLE (500) -> DHL-API Service is not available
	 * - Response::DHL_ERROR_GENERAL (1000)-> General Error
	 * - Response::DHL_ERROR_AUTH_FAILED (1001) -> Authentication has failed
	 * - Response::DHL_ERROR_HARD_VAL_ERROR (1101) -> A hard-validation Error has occurred
	 * - Response::DHL_ERROR_UNKNOWN_SHIPMENT_NUMBER (2000) -> Given Shipment-Number is unknown
	 *
	 * @return int - Status-Code
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * Setter for Status-Code
	 *
	 * - Response::DHL_ERROR_NOT_SET (-1) -> Status-Code was not set
	 * - Response::DHL_ERROR_NO_ERROR (0) -> No Error occurred
	 * - Response::DHL_ERROR_WEAK_WARNING (1) -> A week warning has occurred
	 * - Response::DHL_ERROR_SERVICE_TMP_NOT_AVAILABLE (500) -> DHL-API Service is not available
	 * - Response::DHL_ERROR_GENERAL (1000)-> General Error
	 * - Response::DHL_ERROR_AUTH_FAILED (1001) -> Authentication has failed
	 * - Response::DHL_ERROR_HARD_VAL_ERROR (1101) -> A hard-validation Error has occurred
	 * - Response::DHL_ERROR_UNKNOWN_SHIPMENT_NUMBER (2000) -> Given Shipment-Number is unknown
	 *
	 * @param int $statusCode - Status-Code
	 */
	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
	}

	/**
	 * Getter for Status-Text
	 *
	 * @return string|null - Status-Text or null if not set
	 */
	public function getStatusText() {
		return $this->statusText;
	}

	/**
	 * Setter for Status-Text
	 *
	 * @param string|null $statusText - Status-Text or null for not set
	 */
	public function setStatusText($statusText) {
		$this->statusText = $statusText;
	}

	/**
	 * Getter for Status-Message
	 *
	 * @return string|null - Status-Message or null if not set
	 */
	public function getStatusMessage() {
		return $this->statusMessage;
	}

	/**
	 * Setter for Status-Message
	 *
	 * @param string|null $statusMessage - Status-Message or null for not set
	 */
	public function setStatusMessage($statusMessage) {
		$this->statusMessage = $statusMessage;
	}

	/**
	 * Getter for Sequence-Number
	 *
	 * @return string|null - Sequence-Number of the Request or null if not set
	 */
	public function getShipmentRefNo() {
		return $this->shipmentRefNo;
	}

	/**
	 * Setter for Sequence-Number
	 *
	 * @param string|null $shipmentRefNo - Sequence-Number of the Request or null for not set
	 */
	public function setShipmentRefNo($shipmentRefNo) {
		$this->shipmentRefNo = $shipmentRefNo;
	}

	/**
	 * Getter for Shipment-Number
	 *
	 * @return null|string - Shipment-Number or null if not set
	 */
	public function getShipmentNumber() {
		return $this->shipmentNumber;
	}

	/**
	 * Setter for Shipment-Number
	 *
	 * @param null|string $shipmentNumber - Shipment-Number or null for not set
	 */
	public function setShipmentNumber($shipmentNumber) {
		$this->shipmentNumber = $shipmentNumber;
	}

	public function getRoutingCode(): ?string
	{
		return $this->routingCode;
	}

	public function setRoutingCode(?string $routingCode): void
	{
		$this->routingCode = $routingCode;
	}

	/**
	 * Getter for Return Shipment-Number
	 *
	 * @return null|string - Shipment-Number or null if not set
	 */
	public function getReturnShipmentNumber() {
		return $this->returnShipmentNumber;
	}

	/**
	 * Setter for Return Shipment-Number
	 *
	 * @param null|string $returnShipmentNumber - Shipment-Number or null for not set
	 */
	public function setReturnShipmentNumber($returnShipmentNumber) {
		$this->returnShipmentNumber = $returnShipmentNumber;
	}
	/**
	 * Getter for Label
	 *
	 * @return null|string - Label URL/Base64-Data (Can also contain the return label) or null if not set
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * Setter for Label
	 *
	 * @param null|string $label - Label URL/Base64-Data (Can also contain the return label) or null for not set
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * Getter for ReturnLabel
	 *
	 * @return null|string - Return Label-URL/Base64-Label-Data or null if not requested/set
	 */
	public function getReturnLabel() {
		return $this->returnLabel;
	}

	/**
	 * Setter for ReturnLabel
	 *
	 * @param null|string $returnLabel - Return Label-URL/Base64-Label-Data or null for not requested/set
	 */
	public function setReturnLabel($returnLabel) {
		$this->returnLabel = $returnLabel;
	}

	/**
	 * Getter for Export-Document
	 *
	 * @return null|string - Export-Document Label-URL/Base64-Label-Data or null if not requested/set
	 */
	public function getExportDoc() {
		return $this->exportDoc;
	}

	/**
	 * Setter for Export-Document
	 *
	 * @param null|string $exportDoc - Export-Document Label-URL/Base64-Label-Data or null for not requested/set
	 */
	public function setExportDoc($exportDoc) {
		$this->exportDoc = $exportDoc;
	}

	/**
	 * Getter for Cod-Label
	 *
	 * @return null|string - Cod-Label-URL/Base64-Data or null if not requested/set
	 */
	public function getCodLabel() {
		return $this->codLabel;
	}

	/**
	 * Setter for Cod-Label
	 *
	 * @param null|string $codLabel - Cod-Label-URL/Base64-Data or null if not requested/set
	 */
	public function setCodLabel($codLabel) {
		$this->codLabel = $codLabel;
	}

	/**
	 * Check if the current Status-Code is correct and set the correct one if not
	 */
	public function validateStatusCode() {
		if($this->getStatusCode() === 0 && $this->getStatusText() !== 'OK')
			$this->setStatusCode(Response::DHL_ERROR_WEAK_WARNING);
	}

	/**
	 * @return null
	 */
	public function getLabelFormat()
	{
		return $this->labelFormat;
	}

	/**
	 * @param null $labelFormat
	 */
	public function setLabelFormat($labelFormat): void
	{
		$this->labelFormat = $labelFormat;
	}

	/**
	 * @return null
	 */
	public function getReturnLabelFormat()
	{
		return $this->returnLabelFormat;
	}

	/**
	 * @param null $returnLabelFormat
	 */
	public function setReturnLabelFormat($returnLabelFormat): void
	{
		$this->returnLabelFormat = $returnLabelFormat;
	}

	/**
	 * @return null
	 */
	public function getCustomsDocFormat()
	{
		return $this->customsDocFormat;
	}

	/**
	 * @param null $customsDocFormat
	 */
	public function setCustomsDocFormat($customsDocFormat): void
	{
		$this->customsDocFormat = $customsDocFormat;
	}

	/**
	 * @return null
	 */
	public function getCodLabelFormat()
	{
		return $this->codLabelFormat;
	}

	/**
	 * @param null $codLabelFormat
	 */
	public function setCodLabelFormat($codLabelFormat): void
	{
		$this->codLabelFormat = $codLabelFormat;
	}

	/**
	 * @return null
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param null $token
	 */
	public function setToken($token): void
	{
		$this->token = $token;
	}

	/**
	 * Set all Values of the LabelResponse to this Object
	 *
	 * @param Object $response - LabelData-Response
	 * @since 2.0
	 */
	public function loadLabelData_v3($response) {

		// Get Sequence-Number
		if (isset($response->shipmentRefNo))
			$this->setShipmentRefNo((string)$response->shipmentRefNo);

		// Get Status
		if (isset($response->sstatus)) {
			if (isset($response->sstatus->statusCode))
				$this->setStatusCode((int)$response->sstatus->statusCode);
			if (isset($response->sstatus->title)) {
				if (is_array($response->sstatus->title))
					$this->setStatusText(implode(';', $response->sstatus->title));
				else
					$this->setStatusText($response->sstatus->title);
			}
			if (isset($response->sstatus->detail)) {
				if (is_array($response->sstatus->detail))
					$this->setStatusText(implode(';', $response->sstatus->detail));
				else
					$this->setStatusText($response->sstatus->detail);
			}
			if (isset($response->validationMessages)) {
				if (is_array($response->validationMessages))
					$this->setStatusMessage(implode('; ', array_map(function ($entry) {
						return $entry->validationMessage.": ".$entry->property." (".$entry->validationState.")";
					}, $response->validationMessages)));
				else
					$this->setStatusMessage($response->validationMessages);
			}

			$this->validateStatusCode();
		} else {
			// Error Labels
			if (isset($response->propertyPath)) {
				if (is_array($response->propertyPath))
					$this->setStatusText(implode(';', $response->propertyPath));
				else
					$this->setStatusText($response->propertyPath);
			}
			if (isset($response->message)) {
				if (is_array($response->message))
					$this->setStatusMessage(implode(';', $response->message));
				else
					$this->setStatusMessage($response->message);
			}
		}

		// Get Shipment-Number
		if (isset($response->shipmentNo))
			$this->setShipmentNumber((string)$response->shipmentNo);
		// Get RoutingCode
		if (isset($response->routingCode))
			$this->setRoutingCode((string)$response->routingCode);
		// Get Shipment-Number
		if (isset($response->returnShipmentNo))
			$this->setReturnShipmentNumber((string)$response->returnShipmentNo);

		// Get Label-Data
		if (isset($response->label->url)) {
			$this->setLabel($response->label->url);
			$this->setToken(substr(parse_url($response->label->url)['query'],6));
		}

		else if(isset($response->label->b64))
			$this->setLabel($response->label->b64);
		else if(isset($response->label->zpl2))
			$this->setLabel($response->label->zpl2);

		// Get Return-Label
		if(isset($response->returnLabel->url))
			$this->setReturnLabel($response->returnLabel->url);
		else if(isset($response->returnLabel->b64))
			$this->setReturnLabel($response->returnLabel->b64);
		else if(isset($response->returnLabel->zpl2))
			$this->setReturnLabel($response->returnLabel->zpl2);

		// Get Export-Doc
		if(isset($response->customsDoc->url))
			$this->setExportDoc($response->customsDoc->url);
		else if(isset($response->customsDoc->b64))
			$this->setExportDoc($response->customsDoc->b64);
		else if(isset($response->customsDoc->zpl2))
			$this->setExportDoc($response->customsDoc->zpl2);

		// GET Cod Label
		if(isset($response->codLabel->url))
			$this->setCodLabel($response->codLabel->url);
		else if(isset($response->codLabel->b64))
			$this->setCodLabel($response->codLabel->b64);
		else if(isset($response->codLabel->zpl2))
			$this->setCodLabel($response->codLabel->zpl2);

		if(isset($response->label->printFormat))
			$this->setLabelFormat($response->label->printFormat);
		if(isset($response->returnLabel->printFormat))
			$this->setReturnLabelFormat($response->returnLabel->printFormat);
		if(isset($response->customsDoc->printFormat))
			$this->setCustomsDocFormat($response->customsDoc->printFormat);
		if(isset($response->codLabel->printFormat))
			$this->setCodLabelFormat($response->codLabel->printFormat);
	}

}
