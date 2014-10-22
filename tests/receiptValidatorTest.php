<?php

use Letharion\Apple\itunesReceiptValidator;
use Letharion\Apple\Exceptions\ReceiptNotBase64EncodedException;

class receiptValidatorTest extends PHPUnit_Framework_TestCase {
  public function testThrowsOnBadReceipt() {
    $rv = new itunesReceiptValidator('a', '$[]');
    try {
      $rv->validate();
      $this->assertFalse(TRUE);
    }
    catch (ReceiptNotBase64EncodedException $ex) {
      $this->assertTrue(TRUE);
    }
  }
}
