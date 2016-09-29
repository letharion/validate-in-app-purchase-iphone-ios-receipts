<?php

namespace Letharion\Apple;

use Letharion\Apple\Exceptions\ReceiptNotBase64EncodedException;

class itunesReceiptValidator {

    const SANDBOX_URL    = 'https://sandbox.itunes.apple.com/verifyReceipt';
    const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';

    /**
     * @param string $endpoint
     *   Which endpoint to send the request to.
     *   Expected to be either sandbox or production, see constants in this class.
     * @param string $receipt
     *   The raw receipt data from iTunes that will be passed to iTunes for
     *   validation.
     * @param string $password
     *   A secret shared with iTunes.
     *   Only used for iOS 6 style transaction receipts for auto-renewable
     *   subscriptions. Your app’s shared secret (a hexadecimal string).
     *   @see https://developer.apple.com/library/ios/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html
     */
    function __construct($endpoint, $password = NULL) {
        $this->setEndPoint($endpoint);
        $this->setPassword($password);
    }

    function getPassword() {
        return $this->password;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function getEndpoint() {
        return $this->endpoint;
    }

    function setEndPoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    function validateReceipt($receipt) {
        $response = $this->makeRequest($receipt);

        $decoded_response = $this->decodeResponse($response);

        if (!is_object($decoded_response)) {
            throw new \Exception('Invalid response data' . print_r($decoded_response, TRUE));
        }

        return $decoded_response;
    }

    private function encodeRequest($receipt) {
      $receipt_data = array(
        'receipt-data' => $receipt,
      );

      if (!empty($this->password)) {
        $receipt_data['password'] = $this->password;
      }

      $encoded = json_encode($receipt_data);

      return $encoded;
    }

    private function decodeResponse($response) {
        return json_decode($response);
    }

    private function makeRequest($receipt) {
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeRequest($receipt));

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);

        if ($errno != 0) {
            throw new \Exception($errmsg, $errno);
        }

        return $response;
    }
}
