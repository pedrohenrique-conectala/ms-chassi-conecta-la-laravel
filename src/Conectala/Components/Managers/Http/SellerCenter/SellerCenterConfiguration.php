<?php

namespace Conectala\Components\Managers\Http\SellerCenter;

use Conectala\Components\Managers\Http\HttpConfiguration;

class SellerCenterConfiguration extends HttpConfiguration
{

    public function getPublishersNamespaces(): array
    {
        return [
            "\App\Components\Publishers\Http\SellerCenter\\",
            "\Conectala\Components\Publishers\Http\SellerCenter\\"
        ];
    }

    public function getSubscribersNamespaces(): array
    {
        return [];
    }

}
