<?php

namespace Conectala\CacheWrapper\Handlers\Arguments;

class Arguments
{
    /**
     * @var Argument[]
     */
    private array $arguments;

    public function __construct(mixed ...$args)
    {
        $this->prepare($args);
    }

    protected function prepare(array $args): void
    {
        foreach ($args ?? [] as $position => $value) {
            $this->arguments[] = new Argument(
                $value,
                $position
            );
        }
        $this->arguments = $this->arguments ?? [new Argument(null, null, false)];
    }

    /**
     * @return Argument[]
     */
    public function get(): array
    {
        return array_filter($this->arguments, function (Argument $arg) {
            return $arg->isValid();
        });
    }

    public function valueByPosition(int $position): string|int|bool|null
    {
        return $this->getByPosition($position)->getValue();
    }

    public function getByPosition(int $position): Argument
    {
        return current(array_filter($this->arguments ?? [], function (Argument $arg) use ($position) {
            return $arg->getPosition() === $position;
        })) ?: new Argument(null, $position, false);
    }
}
