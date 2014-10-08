<?php

namespace Letharion\Apple;

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
    function __construct($endpoint, $receipt = NULL, $password = NULL) {
        $this->setEndPoint($endpoint);

        if ($receipt) {
            $this->setReceipt($receipt);
        }
        if ($password) {
            $this->setPassword($password);
        }
    }

    function getReceipt() {
        return $this->receipt;
    }

    function setReceipt($receipt) {
        $this->receipt = $receipt;
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

    function validateReceipt() {
        $response = $this->makeRequest();

        $decoded_response = $this->decodeResponse($response);

        if (!isset($decoded_response->status) || $decoded_response->status != 0) {
            throw new \Exception('Invalid receipt. Status code: ' . (!empty($decoded_response->status) ? $decoded_response->status : 'N/A'));
        }

        if (!is_object($decoded_response)) {
            throw new Exception('Invalid response data');
        }

        return $decoded_response->receipt;
    }

    private function encodeRequest() {
      $receipt_data = array(
        'receipt-data' => $this->getReceipt(),
      );

      if (!empty($this->password)) {
        $receipt_data['password'] = $this->password;
      }

      return json_encode($receipt_data);
    }

    private function decodeResponse($response) {
        return json_decode($response);
    }

    private function makeRequest() {
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeRequest());

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);

        if ($errno != 0) {
            throw new Exception($errmsg, $errno);
        }

        return $response;
    }
}
