<?php
require_once 'inc/config.php';
$session = $_SESSION;
$cart = [];
foreach($session as $keySession => $value){
    if(substr($keySession, 0, 5) == 'cart_'){
        $cart[$keySession] = $value;
    }
}
$price=array_column($cart,'price');
$price=array_sum($price);
$desc=array_column($cart,'name');

$data = array('MerchantID' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
    'Amount' => $price,
    'CallbackURL' => 'http://localhost:8181/ecom/Zverify.php',
    'Description' => implode(', ',$desc));
$jsonData = json_encode($data);
$ch = curl_init('http://sandbox.zarinpal.com/pg/rest/WebGate/PaymentRequest.json');
curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));
$result = curl_exec($ch);
$err = curl_error($ch);
$result = json_decode($result, true);
curl_close($ch);
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    if ($result["Status"] == 100) {
        header('Location: http://sandbox.zarinpal.com/pg/StartPay/' . $result["Authority"]);
    } else {
        echo'ERR: ' . $result["Status"];
    }
}
?>
