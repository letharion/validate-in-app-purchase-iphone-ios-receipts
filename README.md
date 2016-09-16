In App Purchase Receipt Validator - PHP
=======================================

This code allows for server-side validation of iTunes receipts.

For single use, such as debugging and testing, it can be called from CLI:

    $ php ./bin/itunes.php validate @my_receipt.txt "production" @password.txt

Where "my_receipt.txt" is a plain text with a iTunes receipt as a single line,
or multiple receipts delimited by "\n".
Production can be switched for Sandbox as necessary to send the receipt to
the two environments.

Can be called as part of a service:

    <?php
    (new itunesReceiptValidator($endpoint, $password))->validateReceipt($receipt)
