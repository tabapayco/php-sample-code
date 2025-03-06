<?php
include('TabaPay.php');

// Verify Transaction
$request = $_GET;
$merchantToken = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
$token = $request['token'];
$amount = $request['amount'];

if (!empty($_SERVER['HTTP_AUTHORIZE']) && md5($merchantToken) == $_SERVER['HTTP_AUTHORIZE']) {
	$responseData = $_POST;
	if ($responseData['status'] == "success" && $responseData['responseCode'] == 1) {
		// Transaction is verified (auto) successfully 
        // Extract relevant information if needed
        $responseData = array(
            "status" => $responseData['status'],
            "responseCode" => $responseData['responseCode'],
            "message" => $responseData['message'],
            "token" => $responseData['token'],
            "trackingCode" => $responseData['trackingCode'],
			"shaparakRefNumber" => $responseData['shaparakRefNumber'],
            "cardNumber" => $responseData['cardNumber'],
            "hashedCardNumber" => $responseData['hashedCardNumber'],
            "amount" => $responseData['amount'],
            "finalAmount" => $responseData['finalAmount'],
            "ip" => $responseData['ip'],
            "date" => $responseData['date'],
            "additionalData" => !empty($responseData['additionalData']) ? $responseData['additionalData'] : null
        );
        echo json_encode($responseData);
	}
}elseif (!empty($request['status']) && $request['status'] == "success" && $request['responseCode'] == 1) {
    $tabapayAPI = new TabaPayAPI($merchantToken);
	
	$maxAttempts = 3;
	$attempt = 0;
	$responseData = null;
	
	while ($attempt < $maxAttempts && (empty($responseData['status']))) {
		$responseData = $tabaPayAPI->VerifyTransaction($token, $amount);
		$attempt++;
	}

    // Check the verification result
    if ($responseData['status'] == "success" && $responseData['responseCode'] == 1) {
        // Transaction is verified successfully
        // Extract relevant information if needed
        $responseData = array(
            "status" => $responseData['status'],
            "responseCode" => $responseData['responseCode'],
            "message" => $responseData['message'],
            "token" => $responseData['token'],
            "trackingCode" => $responseData['trackingCode'],
			"shaparakRefNumber" => $responseData['shaparakRefNumber'],
            "cardNumber" => $responseData['cardNumber'],
            "hashedCardNumber" => $responseData['hashedCardNumber'],
            "amount" => $responseData['amount'],
            "finalAmount" => $responseData['finalAmount'],
            "ip" => $responseData['ip'],
            "date" => $responseData['date'],
            "additionalData" => !empty($responseData['additionalData']) ? $responseData['additionalData'] : null
        );
        echo json_encode($responseData);
    } else {
        $responseData = array(
            "status" => $responseData['status'],
            "responseCode" => $responseData['responseCode'],
            "message" => $responseData['message'],
            "token" => $responseData['token'],
			"trackingCode" => $responseData['trackingCode'],
            "amount" => $responseData['amount'],
            "finalAmount" => $responseData['finalAmount'],
            "ip" => $responseData['ip'],
            "date" => $responseData['date'],
            "additionalData" => !empty($responseData['additionalData']) ? $responseData['additionalData'] : null
        );
        echo json_encode($responseData);
    }
} else {
    $responseData = array(
        "status" => $request['status'],
        "responseCode" => $request['responseCode'],
        "token" => $request['token'],
        "amount" => $request['amount']
    );
    echo json_encode($responseData);
}
?>