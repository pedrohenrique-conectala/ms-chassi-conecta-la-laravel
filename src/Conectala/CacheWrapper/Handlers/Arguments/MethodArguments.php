<?php

namespace Conectala\CacheWrapper\Handlers\Arguments;

class MethodArguments
{
    /**
     * @var MethodArgument[]
     */
    private array $methodArguments;

    public function add(MethodArgument $methodArgument): void
    {
        if ($methodArgument->isValid()) {
            $this->methodArguments[] = $methodArgument;
        }
    }

    public function mapByNameValue(): array
    {
        $map = array_map(function (MethodArgument $methodArgument) {
            return [$methodArgument->getName() => $methodArgument->getValue()];
        }, $this->methodArguments ?? []);
        return array_reduce($map, 'array_merge', []);
    }
}
