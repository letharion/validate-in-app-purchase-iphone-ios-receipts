In App Purchase Receipt Validator - PHP
=======================================

This code allows for server-side validation of iTunes receipts.

For single use, such as debugging and testing, it can be called from CLI:

    $ php ./bin/itunes.php validate @my_receipt.txt "production" @password.txt

Where "my_receipt.txt" is a plain text file with a iTunes receipt as a single
line, or multiple receipts delimited by "\n". The `@`-sign used for both the
receipt and password causes the validator to use the input as filenames to read
the real data from, as opposed to using the input directly.

Production can be switched for Sandbox as necessary to send the receipt to
the two environments.

Can be called as part of a service:

    <?php
    $endpoint = itunesReceiptValidator::PRODUCTION_URL;
    (new itunesReceiptValidator($endpoint, $password))->validateReceipt($receipt)
