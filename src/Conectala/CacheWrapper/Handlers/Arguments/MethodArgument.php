<?php

namespace Conectala\CacheWrapper\Handlers\Arguments;

class MethodArgument
{
    private static array $ignoredEmpties = ['', 0, '0'];

    private string $name;
    private int $position;
    private mixed $value;

    private bool $optional;
    private bool $valid = true;

    public function __construct(
        private readonly \ReflectionParameter $reflectionParameter,
        private readonly Arguments            $arguments,
    )
    {
        $this->name = $this->reflectionParameter->getName();
        $this->optional = $this->reflectionParameter->isOptional();
        $this->position = $this->reflectionParameter->getPosition();
        $this->prepare();
    }

    protected function prepare(): void
    {
        $this->prepareValue();
        $this->validate();
    }

    protected function prepareValue(): void
    {
        $argument = $this->arguments->getByPosition($this->position);
        $this->value = $argument->wasFound() ? $argument->getValue() : $this->getDefaultValue();
    }

    protected function getDefaultValue(): mixed
    {
        try {
            if ($this->optional) {
                return (new Argument($this->reflectionParameter->getDefaultValue(), $this->position, false))->getValue();
            }
        } catch (\ReflectionException $e) {

        }
        return null;
    }

    protected function validate(): void
    {
        $this->valid = in_array($this->value, self::$ignoredEmpties) || !empty($this->value);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
