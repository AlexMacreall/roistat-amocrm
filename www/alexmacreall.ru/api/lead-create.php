<?php


use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\NumericCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NumericCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\NumericCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Dotenv\Dotenv;

use AmoCRM\Collections\LinksCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;


$rootdir = dirname( __FILE__, 4);

include_once $rootdir . '/vendor/autoload.php';

if (!isset($_GET['product_price'])) {
    exit('INVALID REQUEST: NO PRICE');
}
if(!isset($_GET['client_name']))
{
    exit('INVALID REQUEST: NO NAME');
}
if(!isset($_GET['client_phone']))
{
    exit('INVALID REQUEST: NO PHONE');
}
if(!isset($_GET['client_email']))
{
    exit('INVALID REQUEST: NO EMAIL');
}
$price = +$_GET['product_price'];

$contactName = $_GET['client_name'];
$contactPhone = $_GET['client_phone'];
$contactEmail = $_GET['client_email'];
$isTimeOver30s = $_GET['time_spent'];

$dotenv = new Dotenv;
$dotenv->load('../.env');

$apiClient = new AmoCRMApiClient(
    $_ENV['CLIENT_ID'], $_ENV['CLIENT_SECRET'], $_ENV['CLIENT_REDIRECT_URI']
);

$apiClient->setAccountBaseDomain($_ENV['ACCOUNT_DOMAIN']);

$rawToken = json_decode(file_get_contents('../token.json'), 1);
$token = new AccessToken($rawToken);

$apiClient->setAccessToken($token);

$currentDateTime = date('Y-m-d H:i:s', time());

$productName = "product name";
$leadName = "Новая сделка {$currentDateTime}";



$lead = (new LeadModel)->setName("Новая сделка {$currentDateTime}")
	    ->setPrice($price)
        ->setCustomFieldsValues(
        (new CustomFieldsValuesCollection)
        ->add((new NumericCustomFieldValuesModel)
        ->setFieldId(
                        $_ENV['MORE_THAN_30_SEC_FIELD_ID']
                    )->setValues(
                        (new NumericCustomFieldValueCollection)->add(
                                (new NumericCustomFieldValueModel)->setValue(
                                    $isTimeOver30s
                                )
                            )
                    )
            ));
	
	$lead = $apiClient->leads()->addOne($lead);
echo "OK. LEAD_ID: {$lead->getId()}";






$contact = new ContactModel();
$contact->setName($contactName);

$contact->setCustomFieldsValues(new CustomFieldsValuesCollection());
$customFields = $contact->getCustomFieldsValues();

$emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
        
		$emailField->setValues(
        (new MultitextCustomFieldValueCollection())
            ->add(
                (new MultitextCustomFieldValueModel())
                    ->setEnum('WORK')
                    ->setValue($contactEmail)
            )
    );
	$customFields->add($emailField);
	
	
$phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
$phoneField->setValues(
    (new MultitextCustomFieldValueCollection())
        ->add(
            (new MultitextCustomFieldValueModel())
                ->setEnum('WORK')
                ->setValue($contactPhone)
        )
);
    $customFields->add($phoneField);


try {
    $apiClient->contacts()->addOne($contact);
} catch (AmoCRMApiException $e) {
    printError($e);
    die;
}

$links = new LinksCollection();
$links->add($lead);
try {
    $apiClient->contacts()->link($contact, $links);
} catch (AmoCRMApiException $e) {
    echo $e;
    die;
}

