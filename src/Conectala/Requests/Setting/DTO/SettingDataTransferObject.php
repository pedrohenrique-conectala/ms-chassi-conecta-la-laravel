<?php

namespace App\Http\Requests\Setting\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class SettingDataTransferObject extends DataTransferObject
{
    /** @var string $name */
    public string $name;

    /** @var string $value */
    public string $value;

    /** @var bool $active */
    public bool $active;
}
