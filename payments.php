<?php

require './vendor/autoload.php';

use baibaratsky\WebMoney\Api\X\X3\Request;
use baibaratsky\WebMoney\Exception\CoreException;
use baibaratsky\WebMoney\WebMoney;
use baibaratsky\WebMoney\Signer;
use baibaratsky\WebMoney\Request\Requester\CurlRequester;
use baibaratsky\WebMoney\Api\X\X3;

const WM_ID = '637650381411';
const WM_PURSE = 'R340224679904';
const WM_KEY_PASS = 'Uc0joW89';

$webMoney = new WebMoney(new CurlRequester);

$request = new Request();
$request->setSignerWmid(WM_ID);
$request->setPurse(WM_PURSE);
$request->setStartDateTime(new DateTime('-1 month'));
$request->setEndDateTime(new DateTime('now'));

try {
    $request->sign(
        new Signer(WM_ID, './key/' . WM_ID . '.kwm', WM_KEY_PASS)
    );
} catch (Exception $e) {
}

if ($request->validate()) {
    /** @var X3\Response $response */
    try {
        $response = $webMoney->request($request);
    } catch (CoreException $e) {
    }

    if ($response->getReturnCode() === 0) {
        foreach ($response->getOperations() as $operation) {
            echo 'Trans ID: '. $operation->getTransactionId() . '  Amount: ' . $operation->getAmount() . ' Purses: '
                . $operation->getPayerPurse() . '→' . $operation->getPayeePurse() . ' Balance: ' . $operation->getBalance() . PHP_EOL;
        }
    } else {
        echo 'Error: ' . $response->getReturnDescription();
    }
} else {
    echo 'Request errors: ' . PHP_EOL;
    foreach ($request->getErrors() as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
}
