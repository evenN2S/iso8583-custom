# PHP ISO8583 Parser
[![Build Status](https://travis-ci.org/m1ome/iso8583.svg?branch=master)](https://travis-ci.org/m1ome/iso8583)
[![Coverage Status](https://coveralls.io/repos/github/m1ome/iso8583/badge.svg?branch=master)](https://coveralls.io/github/m1ome/iso8583?branch=master)

# Usage
```php
use ISO8583\Protocol;
use ISO8583\Message;

$iso = new Protocol();
$message = new Message($iso, [
	'lengthPrefix' => 4
]);

// Unpacking message
$message->unpack('02880200F23E4491A8E08020040000000000000016557420000000210000000000000000100009142259180001552359180914020109140000020000000000000600000206000002345574200000002100=02017650000000000PCZ25700015502117986000000000020553LOCATION2              WELWYN GARDEN01GB8260280000456445|0000|PCZ257000155301');

// Packing message
$packedMessage = $message->pack();
```
