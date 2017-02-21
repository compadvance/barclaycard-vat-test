<?php

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

$form_action = 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard_utf8.asp';
$pspid = 'psp id';
$sha_in_passphrase = 'sha in passphrae';

// this works

#$total_amount = 1000;   $item_price = "10.00";    $item_vat = "0.00";

// this works

#$total_amount = 1000;   $item_price = "8.00";    $item_vat = "2.00";

// this works

#$total_amount = 26999;   $item_price = "269.99";    $item_vat = "0.00";


// this won't work, floating point problem occurs
//
// EPDQ is rounding up 225.00 + 44.99 up to 227
//
// Customer gets message
// "Total amount is different to the sum of the details 270/269.99"

$total_amount = 26999;   $item_price = "225.00";    $item_vat = "44.99";

// this works but it should not.

#$total_amount = 26999;   $item_price = "225.01";    $item_vat = "44.99";

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

class Form
{
    private $data = Array();

    public function setValue($key, $value)
    {
        $key = strtoupper($key);
        $this->data[$key] = $value;
    }

    private function getSignature($data, $sign)
    {
        $str = "";
        ksort($data);
        foreach($data as $key => $value) {
            $str .= "{$key}={$value}" . $sign;
        }
        return strtoupper(sha1($str));
    }

    public function getHtmlFormData($sign)
    {
        $data = $this->data;
        $data["SHASign"] = $this->getSignature($data, $sign);
        return $data;
    }
}

$form = new Form();

$form->setValue("PSPID",                                $pspid);
$form->setValue("AMOUNT",                               $total_amount);
$form->setValue("CN",                                   "Joe Doe");
$form->setValue("COM",                                  "Order description");
$form->setValue("CURRENCY",                             "GBP");
$form->setValue("LANGUAGE",                             "en_GB");
$form->setValue("ORDERID",                              uniqid());
$form->setValue("OWNERADDRESS",                         "London Road 3/5");

$form->setValue("OWNERCTY",                             "United Kingdom");
$form->setValue("OWNERTELNO",                           "7123123123");
$form->setValue("OWNERTOWN",                            "London");
$form->setValue("OWNERZIP",                             "SE1 123");

$form->setValue("ECOM_BILLTO_POSTAL_NAME_FIRST",        "Joe");
$form->setValue("ECOM_BILLTO_POSTAL_NAME_LAST",         "Doe");
$form->setValue("ECOM_BILLTO_POSTAL_STREET_NUMBER",     "3");
$form->setValue("ECOM_BILLTO_POSTAL_STREET_LINE1",      "London Road");
$form->setValue("ECOM_BILLTO_POSTAL_POSTALCODE",        "SE1 123");
$form->setValue("ECOM_BILLTO_POSTAL_CITY",              "London");
$form->setValue("ECOM_BILLTO_POSTAL_COUNTRYCODE",       "GB");


$form->setValue("ITEMID1", 12345);
$form->setValue("ITEMNAME1", "HP Laptop");
$form->setValue("ITEMQUANT1", 1);
$form->setValue("ITEMPRICE1", $item_price);
if ($item_vat) {
    $form->setValue("ITEMVAT1", $item_vat);
}


?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    </head>
    <body>
        <form method="POST" action="<?php echo $form_action; ?>" accept-charset="UTF-8">
            <?php foreach($form->getHtmlFormData($sha_in_passphrase) as $key => $value): ?>

                <?php echo htmlspecialchars($key); ?> :

                <input
                    type="text"
                    name="<?php echo htmlspecialchars($key); ?>"
                    value="<?php echo htmlspecialchars($value); ?>"
                /><br/>

            <?php endforeach; ?>
           <input type="submit" />
        </form>
    </body>
</html>