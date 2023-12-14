<?php

namespace Conectala\Components\Publishers;

interface PublisherContract
{
    public function publish(mixed $data): void;
}
