In App Purchase Receipt Validator - PHP
=======================================

This code allows for server-side validation of iTunes receipts.

For single use, such as debugging and testing, it can be called from CLI:

    php ./bin/itunes.php validate @my_receipt.txt "production" @password.txt

Where "my_receipt.txt" is a plain text with a iTunes receipt as a single line.
Production can be switched for Sandbox as necessary to send the receipt to
the two environments.

and as part of a service:

    <?php
    $rv = new itunesReceiptValidator($endpoint, $receipt, $password);
    $rv->validateReceipt();

By calling setReceipt() rather than passing $receipt in, one can easily batch
process many receipts as well.
