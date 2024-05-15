<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once __DIR__.'/vendor/autoload.php';

// Require the Main-Class (other classes will included by this file)
use Jahn\DHL\BusinessShipment;
use Jahn\DHL\Credentials;
use Jahn\DHL\LabelFormat;
use Jahn\DHL\Consignee;
use Jahn\DHL\ReturnReceiver;
use Jahn\DHL\Shipper;
use Jahn\DHL\Services;
use Jahn\DHL\Shipments;
use Jahn\DHL\Customs;
use Jahn\DHL\Details;

$sandbox = true; // Uses the normal test user
//$sandbox = Credentials::DHL_BUSINESS_TEST_USER_THERMO; // Uses the thermo-printer test user
$version = '2.1.8'; // Can be specified or just left out (uses newest by default)
$reference = 'Reference'; // You can use anything here (max 35 chars)
$apiKey = ''; // Set here your ApiKey from developer.dhl.com
// Set this to true then you can skip set the "User", "Signature" and "EKP" (Just for test-Mode) else false or empty
$credentials = new Credentials($sandbox);

if(! $sandbox) { // Not needed if Sandbox
	$credentials->setUser('Your-DHL-Account');	// Don't needed if initialed with Test-Mode
	$credentials->setPassword('Your-DHL-Account-Password'); // Don't needed if initialed with Test-Mode
	$credentials->setEkp('EKP-Account-Number');	// Don't needed if initialed with Test-Mode
}

// Set your API-Key, needed for Sandbox and Production
$credentials->setApiKey($apiKey);			// Api Key from developer.dhl.com

// Set Service stuff (look at the class member - many settings here - just set them you need)
// Set stuff you want in that class - This is very optional
$service = new Services();
$service->setPreferredNeighbour("Schmidt");
//$service->setPreferredLocation("Garage");
$service->setVisualCheckOfAge("A18");
//$service->setNamedPersonOnly(true);
//$service->setIdentCheck(); //need new Class IdentCheck
$service->setEndorsement("RETURN");


$service->setPreferredDay("2023-05-09");
$service->setNoNeighbourDelivery(false);
//$service->setAdditionalInsurance(100, "EUR");
$service->setBulkyGoods(false); // default false
//$service->setCashOnDelivery(); needs new Class BankData
$service->setPremium(true); //default false
//$service->setIndividualSenderRequirement(); // Special instructions for delivery. 2 character code, possible values agreed in contract.
//$service->setClosestDroppoint();
//$service->setParcelOutletRouting(); // Undeliverable domestic shipment can be forwarded and held at retail. Notification to email (fallback: consignee email) will be used.
//$service->setDhlRetoure(); // needs new class ReturnReceiver
//$service->setPostalDeliveryDutyPaid(true); // All import duties are paid by the shipper.


// Set Sender
$sender = new Shipper();
$sender->setName1('Peter Muster');
$sender->setAddressStreet('Test Straße');
$sender->setAddressHouse('12a');
$sender->setPostalCode('21037');
$sender->setCity('Hamburg');
$sender->setCountry('DEU');
$sender->setEmail('kruegge@gmx.de'); // These are super optional, it will printed on the label, can set under receiver as well
$sender->setPhone('01511234567');
$sender->setContactName('Anna Muster');

// Set Consignee
$receiver = new Consignee();
$receiver->setName1('Test Empfänger');
$receiver->setAddressStreet('Test Straße');
$receiver->setAddressHouse('23b');
$receiver->setPostalCode('21037');
$receiver->setCity('Hamburg');
$receiver->setState('State'); // You can set a Province here whenever you need it
$receiver->setCountry('DEU');
$receiver->setEmail('kruegge@gmx.de'); // Needed if you want inform the receiver via mail

//$returnReceiver = new ReturnReceiver(); // Needed if you want to print an return label
//$returnReceiver->setBillingNumber($credentials->getEkp().'0702');
//$returnReceiver->setName1('Test Empfänger');
//$returnReceiver->setAddressStreet('Kirchenheerweg');
//$returnReceiver->setAddressHouse('23b');
//$returnReceiver->setPostalCode('21037');
//$returnReceiver->setCity('Hamburg');
//$returnReceiver->setState('State'); // You can set a Province here whenever you need it
//$returnReceiver->setCountry('DEU');
//$service->setDhlRetoure($returnReceiver);

// If want to use it, please set Address etc of the return receiver to!

$customs = new Customs(); // Needed if you want to send out of europe, also add items

$details = new Details();
$details->setWeightValue(2);
$details->setWeightUom("kg"); // possible g or kg

// Set Shipment Details
$shipments = new Shipments($credentials->getEkp(10) . '0102'); // Create a Shipment-Details with the first 10 digits of your EKP-Number and 0101 (?)
$shipments->setShipDate(date('Y-m-d')); // Optional: YYYY-MM-DD Need to be in the future and NOT on a sunday | null or drop it, to use today
$shipments->setRefNo($reference); // Just needed to identify the shipment if you do multiple
$shipments->setCostCenter("String"); // Optional a costcenter
$shipments->setCreationSoftware("String"); // Optional identify sender of request
$shipments->setShipper($sender);
$shipments->setConsignee($receiver); // You can set these Object-Types here: DHL_Filial, DHL_Receiver & DHL_Locker
//$shipments->setReturnReceiver($returnReceiver); // Needed if you want print a return label
$shipments->setServices($service); // Optional, just needed if you add some services
$shipments->setDetails($details); //set Weight and size of Package
//$shipments->setCustoms($customs); // Optional if you need Custom declaration for out of Europe

// If you want to specify the Label-Format you can add this optional Object here: (since 3.0)
$labelFormat = new LabelFormat();
// Everything is optional in that object, you can overwrite default values by setting them
// Label & LabelRetoure Format can be any of the se values:
/* A4 OR LabelFormat::FORMAT_A4
 * 910-300-700 OR LabelFormat::FORMAT_910_300_700
 * 910-300-700-oZ OR LabelFormat::FORMAT_910_300_700_OZ
 * 910-300-600 OR LabelFormat::FORMAT_910_300_600
 * 910-300-610 OR LabelFormat::FORMAT_910_300_610
 * 910-300-710 OR LabelFormat::FORMAT_910_300_710
 *
 * or null/'GUI'/LabelFormat::FORMAT_DEFAULT for DHL-Default
 */
$labelFormat->setLabelFormat(null);
$labelFormat->setLabelFormatRetoure(null);
$labelFormat->setCombinedPrinting('false'); // Here you can set if all labels should printed together (if you have multiple)
$labelFormat->setLabelResponseType("URL"); // URL or INCLUDE
$labelFormat->setDocFormat("PDF");

// Required just Credentials also accept Test-Mode and Version
$dhl = new BusinessShipment($credentials, /*Optional*/$sandbox, /*Optional*/$version);
$dhl->setProfile('STANDARD_GRUPPENPROFIL'); // here you can set the group profile name if needed
$dhl->setPrintOnlyIfReceiverIsValid(false); // Label only generated if Adress is valid
// You can also add the Label-Format if you have that object: (else it uses default - since 3.0)
$dhl->setLabelFormat($labelFormat);
// Add the ShipmentOrder to the BusinessShipment Object, you can add up to 30 ShipmentOrder Objects in 1 call
$dhl->setShipments($shipments);


//$response = $dhl->validateShipment(); // Validate the request
//print_r($response);
// or
$response = $dhl->createShipment(); // Creates the request
print_r($response);
// For deletion you just need the shipment number and credentials
//$dhlDel = new BusinessShipment($credentials, $sandbox, $version);
//$response_del = $dhlDel->deleteShipment('shipment_number1'); // Deletes a Shipment
//$response_del = $dhlDel->deleteShipment(array('shipment_number1', 'shipment_number2')); // Deletes multiple Shipments (up to 30)

// To re-get the Label you can use the getShipmentLabel method - the shipment must be created with createShipment before
//$dhlReGetLabel = new BusinessShipment($credentials, $sandbox, $version);
//$dhlReGetLabel->setLabelFormat($labelFormat); // we need to know in which format
//$reGetLabelResponse = $dhlReGetLabel->getShipmentLabel('0034043333301020000064577'); // ReGet a single Label
//$reGetLabelResponse = $dhlReGetLabel->getLabel(array('shipment_number1', 'shipment_number2'), DHL_BusinessShipment::RESPONSE_TYPE_B64); // ReGet multiple Labels (up to 30)

// To do a Manifest-Request you can use the doManifest method - you have to provide a Shipment-Number
//$manifestDHL = new BusinessShipment($credentials, $sandbox, $version);
//$manifestResponse = $manifestDHL->doManifest(null, $credentials->getEkp(10) . '0102'); // Does Manifest on a Shipment, if empty all open shippings
//$manifestResponse = $manifestDHL->doManifest(array('shipment_number1', 'shipment_number2'), $credentials->getEkp(10) . '0102'); // Does Manifest on multiple Shipments (up to 30)

// To do a Manifest-Request you can use the doManifest method - you have to provide a Shipment-Number
//$getManifestDHL->setLabelFormat($labelFormat); // we need to know in which format
//$getManifestResponse = $getManifestDHL->getManifest('2023-05-01', $credentials->getEkp(10) . '0102'); // Need to be in the past or today after doManifest()

// Get the result (just use var_dump to show all results)
//if($response !== false)
//	var_dump($response);
//else
//	var_dump($dhl->getErrors());
