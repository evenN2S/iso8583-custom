# PHP ISO8583 Parser
[![Build Status](https://travis-ci.org/evenN2S/iso8583-custom.svg?branch=master)](https://travis-ci.org/evenN2S/iso8583-custom)
[![Coverage Status](https://coveralls.io/repos/github/evenN2S/iso8583-custom/badge.svg?branch=master)](https://coveralls.io/github/evenN2S/iso8583-custom?branch=master)

# Usage (default)
```php
use ISO8583\Protocol;
use ISO8583\Message;

$iso = new Message(new Protocol(), [
    'lengthPrefix' => 4 # Or you can ignore this, default length is 4
]);

# Unpacking message
$iso->unpack('01680200F2384001888080040000000006000000161111222233334444470040000001000000092510530800005217530809256010035030350318092500005211112222360004201811101150201041110110131374');

# Packing message
$packed = $iso->pack();
```

# Usage (custom)
```php
use ISO8583\Protocol;
use ISO8583\CustomMessage;

$iso = new CustomMessage(new Protocol(), [
    'lengthPrefix' => 4 # Or you can ignore this, default length is 4
]);

# Unpacking message
$message->unpack('00B401047238000000C1000C16888878100000090500000000000500000012312359590000032359591231323232323232323231313131313131313131313131313100964AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009000831323334353637380006303030303031');

# Packing message
$packed = $message->pack();
```

# Usage (custom with HPDH mode)
```php
use ISO8583\Protocol;
use ISO8583\CustomMessage;

$iso = new CustomMessage(new Protocol(), [
    'mode' => CustomMessage::MODE_HPDH
]);

# Unpacking message
$iso->unpack('00656000018053080082220000000100000400000000000000123123595900000312310064454339444636393533353346413037354246464136313030303030303030303030303030303030303030303030303030303030303030303030303030303030300101');

# Packing message
$packed = $iso->pack();
```