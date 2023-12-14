<?php

namespace Conectala\Components\Managers;

interface ComponentManagerConfigurationContract
{
    public function handler(): void;

    public function loadedDefinitions(): array;
}
