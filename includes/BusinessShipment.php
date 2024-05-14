<?php

namespace Jahn\DHL;

/**
 * Author: Peter Dragicevic [peter@petschko.org]
 * Authors-Website: https://petschko.org/
 * Modified for new API developer.dhl.com from Jahn on 01.05.2023
 * Date: 26.01.2017
 * Time: 15:37
 *
 * Notes: Contains all Functions/Values for DHL-Business-Shipment
 *
 * Checkout the repo which inspired me to improve this:
 * @link https://github.com/tobias-redmann/dhl-php-sdk
 */

use Exception;
use stdClass;

/**
 * Class BusinessShipment
 *
 * @package Jahn\DHL
 */
class BusinessShipment extends Version {
	/**
	 * DHL Origin WSDL-Lib-URL
	 */
	const DHL_WSDL_LIB_URL = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/';

	/**
	 * DHL-Sandbox REST-URL
	 */
	const DHL_SANDBOX_URL = 'https://api-sandbox.dhl.com/parcel/de/shipping/v2/';

	/**
	 * DHL-Live REST-URL
	 */
	const DHL_PRODUCTION_URL = 'https://api-eu.dhl.com/parcel/de/shipping/v2/';

	/**
	 * Newest-Version
	 */
	const NEWEST_VERSION = '2.1.8';

	/**
	 * Response-Type URL
	 */
	const RESPONSE_TYPE_URL = 'URL';

	/**
	 * Response-Type Base64
	 */
	const RESPONSE_TYPE_B64 = 'B64';

	/**
	 * Response-Type XML
	 */
	const RESPONSE_TYPE_XML = 'XML';

	/**
	 * Response-Type ZPL2
	 */
	const RESPONSE_TYPE_ZPL2 = 'ZPL2';

	/**
	 * Maximum requests to DHL in one call
	 */
	const MAX_DHL_REQUESTS = 30;

	/**
	 * Contains the error array
	 *
	 * @var string[] $errors - Error-Array
	 */
	private $errors = array();

	// Setting-Fields
	/**
	 * Contains if the Object runs in Sandbox-Mode
	 *
	 * @var bool $test - Is Sandbox-Mode
	 */
	private bool $test;

	// Object-Fields
	/**
	 * Contains the Credentials Object
	 *
	 * Notes: Is required every time! Used to login
	 *
	 * @var Credentials $credentials - Credentials Object
	 */
	private $credentials;

	/**
	 * Contains if the label will be only be printable, if the receiver address is valid.
	 *
	 * Note: Optional
	 *
	 * @var string|null $printOnlyIfReceiverIsValid - true will only print if receiver address is valid else false (null uses default)
	 */
	private $printOnlyIfReceiverIsValid = 'false';

	/**
	 * Contains the Shipment Details
	 *
	 * @var Shipments $shipments - Shipment Details Object
	 ** @see ShipmentOrder
	 */
	private $shipments = array();

	// Fields
	/**
	 * Contains the Profile
	 *
	 * Min-Len: -
	 * Max-Len: 30
	 *
	 * @var string $profile -
	 */
	private string $profile = 'STANDARD_GRUPPENPROFIL';

	/**
	 * Contains the Label-Format
	 *
	 * Note: Optional
	 *
	 * @var LabelFormat|null $labelFormat - Label-Format (null for DHL-Default)
	 * @since 3.0
	 */
	private $labelFormat = null;

	/**
	 * BusinessShipment constructor.
	 *
	 * @param Credentials $credentials - DHL-Credentials-Object
	 * @param bool|string $sandbox - Use a specific Sandbox-Mode or Production-Mode
	 * 					Test-Mode (Normal): Credentials::TEST_NORMAL, 'test', true
	 * 					Test-Mode (Thermo-Printer): Credentials::TEST_THERMO_PRINTER, 'thermo'
	 * 					Live (No-Test-Mode): false - default
	 * @param null|string $version - Version to use or null for the newest
	 */
	public function __construct($credentials, $sandbox = false, $version = null) {
		// Set Version
		if($version === null)
			$version = self::NEWEST_VERSION;

		parent::__construct($version);

		// Set Test-Mode
		$this->setTest($sandbox);

		// Set Credentials
		if($this->isTest()) {
			$c = new Credentials($sandbox);
			$c->setUser($credentials->getUser());
			$c->setPassword($credentials->getPassword());
			$c->setApiKey($credentials->getApiKey());
			$credentials = $c;
		}
		$this->setCredentials($credentials);
	}

	/**
	 * Get Error-Array
	 *
	 * @return string[] - Error-Array
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Set Error-Array
	 *
	 * @param string[] $errors - Error-Array
	 */
	public function setErrors($errors) {
		$this->errors = $errors;
	}

	/**
	 * Adds an Error to the Error-Array
	 *
	 * @param string $error - Error-Message
	 */
	public function addError($error) {
		$this->errors[] = $error;
	}

	/**
	 * Returns if this instance run in Test-Mode / Sandbox-Mode
	 *
	 * @return bool - Runs in Test-Mode / Sandbox-Mode
	 */
	public function isTest() {
		return $this->test;
	}

	/**
	 * Set if this instance runs in Test-Mode / Sandbox-Mode
	 *
	 * @param bool $test - Runs in Test-Mode / Sandbox-Mode
	 */
	public function setTest($test) {
		$this->test = $test;
	}


	/**
	 * Get Credentials-Object
	 *
	 * @return Credentials - Credentials-Object
	 */
	public function getCredentials() {
		return $this->credentials;
	}

	/**
	 * Set Credentials-Object
	 *
	 * @param Credentials $credentials - Credentials-Object
	 */
	public function setCredentials($credentials) {
		$this->credentials = $credentials;
	}


	/**
	 * Get the Profile
	 *
	 * @return string - Profile
	 *
	 */
	public function getProfile() {
		return $this->profile;
	}

	/**
	 * Set the Profile
	 *
	 * @param string $profile
	 *
	 */
	public function setProfile($profile) {
		$this->profile = $profile;
	}

	/**
	 * Clears the Shipment-Order list
	 */
	public function clearShipmens() {
		$this->setShipments(array());
	}

	/**
	 * Returns how many Shipment-Orders are in this List
	 *
	 * @return int - Shipments Count
	 */
	public function countShipments() {
		return count($this->getShipments());
	}

	/**
	 * Get the Label-Format
	 *
	 * @return LabelFormat|null - Label-Format or null for DHL-Default
	 * @since 3.0
	 */
	public function getLabelFormat(): ?LabelFormat {
		return $this->labelFormat;
	}

	/**
	 * Set the Label-Format
	 *
	 * @param LabelFormat|null $labelFormat - Label-Format or null for DHL-Default
	 * @since 3.0
	 */
	public function setLabelFormat(?LabelFormat $labelFormat): void {
		$this->labelFormat = $labelFormat;
	}

	/**
	 * @return string|null
	 */
	public function getPrintOnlyIfReceiverIsValid(): ?string
	{
		return $this->printOnlyIfReceiverIsValid;
	}

	/**
	 * @param string|null $printOnlyIfReceiverIsValid
	 */
	public function setPrintOnlyIfReceiverIsValid(?string $printOnlyIfReceiverIsValid): void
	{
		$this->printOnlyIfReceiverIsValid = $printOnlyIfReceiverIsValid;
	}

	/**
	 * @return array|Shipments
	 */
	public function getShipments()
	{
		return $this->shipments;
	}

	/**
	 * @param array|Shipments $shipments
	 */
	public function setShipments($shipments): void
	{
		$this->shipments[] = $shipments;
	}

	/**
	 * Check if the request-Array is to long
	 *
	 * @param array $array - Array to check
	 * @param string $action - Action of the request
	 * @param int $maxReq - Maximum-Requests - Default: self::MAX_DHL_REQUESTS
	 */
	public function checkRequestCount($array, $action, $maxReq = self::MAX_DHL_REQUESTS) {
		$count = count($array);

		if($count > self::MAX_DHL_REQUESTS)
			$this->addError('There are only ' . $maxReq . ' Request/s for one call allowed for the action "'
				. $action . '"! You tried to request ' . $count . ' ones');
	}

	/**
	 * Gets the current (local)-Version or Request it via Rest from DHL
	 *
	 * @param bool $viaSOAP - Request the Version from DHL (Default: false - get local-version as string)
	 * @param bool $getBuildNumber - Return the Build number as well (String look then like this: 2.2.12) Only possible via SOAP - Default false
	 * @param bool $returnAsArray - Return the Version as Array - Default: false
	 * @return bool|array|string - Returns the Version as String|array or false on error
	 */
	public function getVersion($viaSOAP = false, $getBuildNumber = false, $returnAsArray = false) {
		if(! $viaSOAP) {
			if($returnAsArray)
				return array(
					'mayor' => parent::getMayor(),
					'minor' => parent::getMinor()
				);
			else
				return parent::getVersion();
		}

		switch($this->getMayor()) {
			case 1:
				return false;
			case 2:
			default:
				$data = $this->getVersionClass();
		}

		try {
			$response = $this->sendGetVersionRequest($data);
		} catch(Exception $e) {
			$this->addError($e->getMessage());

			return false;
		}

		if(is_soap_fault($response)) {
			$this->addError($response->faultstring);

			return false;
		} else {
			if($returnAsArray)
				return array(
					'mayor' => $response->Version->majorRelease,
					'minor' => $response->Version->minorRelease,
					'build' => $response->Version->build
				);
			else
				return $response->Version->majorRelease . '.' . $response->Version->minorRelease .
					(($getBuildNumber) ? '.' . $response->Version->build : '');
		}
	}

	/**
	 * Creates the getManifest-Request
	 *
	 * @param string|string[] $billingNumber - Billingnumber you want to get the manifest for
	 * @param string|string[] $date - Shipment-Number(s) for Manifest (up to 30 Numbers)
	 * @return Response - false on error or DHL-Response Object
	 */
	public function getManifest($billingNumber, $date=null ) {

		if($this->isTest())
			$location = self::DHL_SANDBOX_URL;
		else
			$location = self::DHL_PRODUCTION_URL;

		$query = array();
		$query['date'] = $date; // true or false
		$query['includeDocs'] = $this->getLabelFormat()->getLabelResponseType(); // include or URL
		$query['billingNumber'] = $billingNumber;

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $location."manifests?".http_build_query($query),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_USERPWD => $this->getCredentials()->getUser() . ':' . $this->getCredentials()->getPassword(),
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"Accept-Language: de-DE",
				"Dhl-Api-Key: ".$this->getCredentials()->getApiKey(),
				"content-type: application/json",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if (strpos($response, "502 Bad Gateway") !== false) {
			error_log(print_r($response,true));
			$this->addError(__FUNCTION__ . ': 502 Bad Gateway!');
			return false;
		}

		if ($err) {
			error_log("cURL Error #:" . $err );
			$this->addError(__FUNCTION__ . ': '.$err);
			return false;
		} else {
			$response = json_decode(json_encode(json_decode($response, true)));
			return new Response($this->getVersion(), $response);
		}
		return new Response($this->getVersion(), $response);
	}

	/**
	 * Creates the getManifest-Request
	 *
	 * @param string|int $shipmentNumbers - Shippingnumbers where Manifest do for
	 * @param string|int $billingNumber - Billingnumber where Manifest do for
	 * @return Response - false on error or DHL-Response Object
	 */
	public function doManifest($billingNumber, $shipmentNumbers=null) {

		if($this->isTest())
			$location = self::DHL_SANDBOX_URL;
		else
			$location = self::DHL_PRODUCTION_URL;

		$addstring='';
		if($shipmentNumbers!== null)
			$addstring='&all=true';

		$data = array();
		$data['profile'] = $this->getProfile(); // true or false
		$data['shipmentNumbers'] = array($shipmentNumbers); // include or URL
		$data['billingNumber'] = $billingNumber;

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $location."manifests?".$addstring,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_USERPWD => $this->getCredentials()->getUser() . ':' . $this->getCredentials()->getPassword(),
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => [
				"Accept-Language: de-DE",
				"Dhl-Api-Key: ".$this->getCredentials()->getApiKey(),
				"content-type: application/json",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if (strpos($response, "502 Bad Gateway") !== false) {
			error_log(print_r($response,true));
			$this->addError(__FUNCTION__ . ': 502 Bad Gateway!');
			return false;
		}

		if ($err) {
			error_log("cURL Error #:" . $err );
			$this->addError(__FUNCTION__ . ': '.$err);
			return false;
		} else {
			$response = json_decode(json_encode(json_decode($response, true)));
			return new Response($this->getVersion(), $response);
		}
		return new Response($this->getVersion(), $response);
	}


	/**
	 * Creates the Shipment-Request
	 *
	 * @return bool|Response - false on error or DHL-Response Object
	 */
	public function createShipment() {
		switch($this->getMayor()) {
			case 1:
				return false;
			case 2:
			default:
				$data = $this->createShipmentClass_v3();
		}

		$response = null;

		// Create Shipment
		try {
			$response = $this->sendShipmentRequest($data);
		} catch(Exception $e) {
			$this->addError($e->getMessage());

			return false;
		}

		return $response;
	}


	/**
	 * Creates the Data-Object for the Request
	 *
	 * @param null|string $shipmentNumber - Shipment Number which should be included or null for none
	 * @return StdClass - Data-Object
	 * @since 3.0
	 */
	public function createShipmentClass_v3($shipmentNumber = null) {
		$shipments = $this->getShipments();
		$this->checkRequestCount($shipments, 'createShipmentClass');

		$data = new StdClass;
		$data->profile = $this->getProfile();

		foreach($shipments as $key => &$shipment) {
			$data->shipments[$key] = $shipment->getShipmentsClass_v3();
		}

		//$data->labelResponseType = $this->getLabelResponseType();

		/*if($this->getLabelFormat() !== null)
			$data->labelFormat = $this->getLabelFormat()->getLabelFormat();

		if($this->getWeight() !== null)
			$data->weight = $this->getWeight()->getWeight();

		if($this->getLabelFormat() !== null)
			$data->labelFormatRetoure = $this->getLabelFormat()->getLabelFormatRetoure(); // todo check if correct (can it always be set?)

		if($this->getLabelFormat() !== null)
			$data->combinedPrinting = $this->getLabelFormat()->getCombinedPrinting();*/

		return $data;
	}

	/**
	 * deleteShipmentOrder
	 *
	 * Deletes a Shipment
	 *
	 * @param string|string[] $shipmentNumbers - Shipment-Number(s) of the Shipment(s) to delete (up to 30 Numbers)
	 * @return Response - Response
	 */
	public function deleteShipment($shipmentNumbers) {
		if(!is_array($shipmentNumbers)) {
			$shipmentNumbers=array(0=>$shipmentNumbers);
		}
		$addstring="";
		foreach($shipmentNumbers as $shipmentNumber) {
			$addstring.='&shipment='.$shipmentNumber;
		}

		if($this->isTest())
			$location = self::DHL_SANDBOX_URL;
		else
			$location = self::DHL_PRODUCTION_URL;


		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $location."orders?profile=".$this->getProfile().$addstring,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_USERPWD => $this->getCredentials()->getUser() . ':' . $this->getCredentials()->getPassword(),
			CURLOPT_CUSTOMREQUEST => "DELETE",
			CURLOPT_HTTPHEADER => [
				"Accept: application/json",
				"Dhl-Api-Key: ".$this->getCredentials()->getApiKey(),
				"Accept-Language: de-DE",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if (strpos($response, "502 Bad Gateway") !== false) {
			error_log(print_r($response,true));
			$this->addError(__FUNCTION__ . ': 502 Bad Gateway!');
			return false;
		}

		if ($err) {
			error_log("cURL Error #:" . $err );
			$this->addError(__FUNCTION__ . ': '.$err);
			return false;
		} else {
			$response = json_decode(json_encode(json_decode($response, true)));
			return new Response($this->getVersion(), $response);
		}
		return new Response($this->getVersion(), $response);
	}


	/**
	 * Alias for getLabel
	 *
	 * Requests a Shipment-Label again
	 *
	 * @param string|string[] $shipmentNumbers - Shipment-Number(s) of the Label(s) (up to 30 Numbers)
	 * @return Response - Response or false on error
	 */
	public function getShipmentLabel($shipmentNumbers) {
		if(!is_array($shipmentNumbers)) {
			$shipmentNumbers=array(0=>$shipmentNumbers);
		}
		$addstring="";
		foreach($shipmentNumbers as $shipmentNumber) {
			$addstring.='&shipment='.$shipmentNumber;
		}

		if($this->isTest())
			$location = self::DHL_SANDBOX_URL;
		else
			$location = self::DHL_PRODUCTION_URL;

		$query = array();
		$query['includeDocs'] = $this->getLabelFormat()->getLabelResponseType(); // include or URL
		$query['docFormat'] = $this->getLabelFormat()->getDocFormat(); // PDF or ZPL2
		$query['printFormat'] = $this->getLabelFormat()->getLabelFormat(); // see https://developer.dhl.com/api-reference/parcel-de-shipping-post-parcel-germany-v2#operations-Shipments_and_Labels-createOrders
		$query['retourePrintFormat'] = $this->getLabelFormat()->getLabelFormatRetoure(); // see https://developer.dhl.com/api-reference/parcel-de-shipping-post-parcel-germany-v2#operations-Shipments_and_Labels-createOrders
		$query['combine'] = $this->getLabelFormat()->getCombinedPrinting(); // true or false

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $location."orders?profile=".$this->getProfile().$addstring."&".http_build_query($query),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_USERPWD => $this->getCredentials()->getUser() . ':' . $this->getCredentials()->getPassword(),
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"Accept: application/json",
				"Dhl-Api-Key: ".$this->getCredentials()->getApiKey(),
				"Accept-Language: de-DE",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if (strpos($response, "502 Bad Gateway") !== false) {
			error_log(print_r($response,true));
			$this->addError(__FUNCTION__ . ': 502 Bad Gateway!');
			return false;
		}

		if ($err) {
			error_log("cURL Error #:" . $err );
			$this->addError(__FUNCTION__ . ': '.$err);
			return false;
		} else {
			$response = json_decode(json_encode(json_decode($response, true)));
			return new Response($this->getVersion(), $response);
		}
		return new Response($this->getVersion(), $response);
	}


	/**
	 * getLabel
	 *
	 * Requests raw Data of Shipment-Label again
	 *
	 * @param string|string[] $token - token from a label url
	 * @return Response - Response or false on error
	 */
	public function getLabel($token) {
		if($this->isTest())
			$location = self::DHL_SANDBOX_URL;
		else
			$location = self::DHL_PRODUCTION_URL;

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $location.'labels?token='.$token,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_USERPWD => $this->getCredentials()->getUser() . ':' . $this->getCredentials()->getPassword(),
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"Accept: application/pdf",
				"Accept-Language: de-DE",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if (strpos($response, "502 Bad Gateway") !== false) {
			error_log(print_r($response,true));
			$this->addError(__FUNCTION__ . ': 502 Bad Gateway!');
			return false;
		}

		if ($err) {
			error_log("cURL Error #:" . $err );
			$this->addError(__FUNCTION__ . ': '.$err);
			return false;
		} else {
			$response = json_decode(json_encode(json_decode($response, true)));
			return new Response($this->getVersion(), $response);
		}
		return new Response($this->getVersion(), $response);
	}

	/**
	 * Validates a Shipment
	 *
	 * @return bool|Response - Response or false on error
	 */
	public function validateShipment() {
		switch($this->getMayor()) {
			case 1:
				return false;
			case 2:
			default:
				$data = $this->createShipmentClass_v3();
		}

		$response = null;

		try {
			$response = $this->sendShipmentRequest($data, 'true');
		} catch(Exception $e) {
			$this->addError($e->getMessage());

			return false;
		}
		return $response;
	}

	/**
	 * Requests the Validation of a Shipment
	 *
	 * @param Object|array $data - Shipment-Data
	 * @return Object - DHL-Response
	 */
	public function sendShipmentRequest($data, $validate='false') {
print_r($data);
		if($this->isTest())
			$location = self::DHL_SANDBOX_URL;
		else
			$location = self::DHL_PRODUCTION_URL;

		$query = array();
		$query['validate'] = $validate; // true or false
		$query['mustEncode'] = $this->getPrintOnlyIfReceiverIsValid(); // true or false
		$query['includeDocs'] = $this->getLabelFormat()->getLabelResponseType(); // include or URL
		$query['docFormat'] = $this->getLabelFormat()->getDocFormat(); // PDF or ZPL2
		$query['printFormat'] = $this->getLabelFormat()->getLabelFormat(); // see https://developer.dhl.com/api-reference/parcel-de-shipping-post-parcel-germany-v2#operations-Shipments_and_Labels-createOrders
		$query['retourePrintFormat'] = $this->getLabelFormat()->getLabelFormatRetoure(); // see https://developer.dhl.com/api-reference/parcel-de-shipping-post-parcel-germany-v2#operations-Shipments_and_Labels-createOrders
		$query['combine'] = $this->getLabelFormat()->getCombinedPrinting(); // true or false

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $location."orders?".http_build_query($query),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_USERPWD => $this->getCredentials()->getUser() . ':' . $this->getCredentials()->getPassword(),
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => [
				"Accept-Language: de-DE",
				"Dhl-Api-Key: ".$this->getCredentials()->getApiKey(),
				"content-type: application/json",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if (strpos($response, "502 Bad Gateway") !== false) {
			error_log(print_r($response,true));
			$this->addError(__FUNCTION__ . ': 502 Bad Gateway!');
			return false;
		}

		if ($err) {
			error_log("cURL Error #:" . $err );
			$this->addError(__FUNCTION__ . ': '.$err);
			return false;
		} else {
			$response = json_decode(json_encode(json_decode($response, true)));
			print_r($response);
			return new Response($this->getVersion(), $response);
		}
		return new Response($this->getVersion(), $response);
	}
}
