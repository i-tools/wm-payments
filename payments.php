<?php

require './vendor/autoload.php';

use baibaratsky\WebMoney\Api\X\X3\Request;
use baibaratsky\WebMoney\Exception\CoreException;
use baibaratsky\WebMoney\WebMoney;
use baibaratsky\WebMoney\Signer;
use baibaratsky\WebMoney\Request\Requester\CurlRequester;
use baibaratsky\WebMoney\Api\X\X3;

$webMoney = new WebMoney(new CurlRequester);

$request = new Request();
$request->setSignerWmid('637650381411'); // WMID
$request->setPurse('R340224679904'); // КОшелек
$request->setStartDateTime(new DateTime('-1 month'));
$request->setEndDateTime(new DateTime('now'));

$request->sign(
    new Signer('637650381411', './key/637650381411.kwm', 'Uc0joW89')
);

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
