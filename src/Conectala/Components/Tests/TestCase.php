<?php

namespace Conectala\Components\Tests;

use Conectala\Components\Tests\Support\Traits\DatabaseMigrations;
use Conectala\Components\Tests\Support\Traits\MockFunctions;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUpTraits()
    {
        parent::setUpTraits();
        $uses = array_flip(class_uses_recursive(get_class($this)));

        if (isset($uses[DatabaseMigrations::class])) {
            $this->runDatabaseMigrations();
        }
        if (isset($uses[MockFunctions::class])) {
            $this->functionsMocker();
        }
    }
}
