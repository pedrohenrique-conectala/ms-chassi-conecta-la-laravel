<?php

namespace Conectala\Components\Publishers\Http\SellerCenter;

use Conectala\Components\Managers\Http\SellerCenter\SellerCenterConfiguration;
use Conectala\Components\Publishers\Http\BasePublisher;

class SellerCenterPublisher extends BasePublisher
{
    public function __construct(SellerCenterConfiguration $sellerCenterConfiguration, mixed ...$args)
    {
        parent::__construct($sellerCenterConfiguration, ...$args);
    }
}
