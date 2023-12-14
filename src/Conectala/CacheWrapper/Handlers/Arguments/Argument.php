<?php

namespace Conectala\CacheWrapper\Handlers\Arguments;

class Argument
{
    private static array $specialTypes = ['array', 'object', 'resource'];
    private static array $invalidTypes = [\Closure::class];

    private string|int|bool|null $value;
    private bool $valid;
    private string $typeOf;

    public function __construct(
        private readonly mixed    $arg,
        private readonly int|null $position,
        private readonly bool     $found = true
    )
    {
        $this->typeOf = gettype($this->arg);
        $this->validate();
        $this->setValue($this->arg);
    }

    protected function setValue(mixed $value): void
    {
        if ($this->isValid() && $this->isSpecialType()) {
            $value = $this->serialize($value);
        }
        settype($value, gettype($value));
        $this->value = $value;
    }

    protected function serialize(mixed $value): string
    {
        return md5(serialize($value));
    }

    protected function validate(): void
    {
        $this->valid = (function () {
            return empty(array_filter(self::$invalidTypes, function (string $type) {
                return is_a($this->arg, $type);
            }));
        })();
    }

    public function getValue(): bool|int|string|null
    {
        return $this->value;
    }

    protected function isSpecialType(): bool
    {
        return in_array($this->typeOf, self::$specialTypes);
    }

    public function getPosition(): int|null
    {
        return $this->position;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function wasFound(): bool
    {
        return $this->found;
    }
}
