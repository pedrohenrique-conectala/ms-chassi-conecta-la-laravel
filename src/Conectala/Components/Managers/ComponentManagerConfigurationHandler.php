<?php

namespace Conectala\Components\Managers;

/**
 * Class ComponentManagerConfigurationHandler
 * @package Conectala\Components\Managers
 * @property ComponentManagerConfigurationContract[] $componentManagerContracts
 */
class ComponentManagerConfigurationHandler
{
    protected static ComponentManagerConfigurationHandler $instance;

    /**
     * @var ComponentManagerConfigurationContract[]
     */
    protected array $managerComponents = [];

    private function __construct()
    {
    }

    public static function instance(): ComponentManagerConfigurationHandler
    {
        if (!isset(ComponentManagerConfigurationHandler::$instance) || ComponentManagerConfigurationHandler::$instance === null) {
            ComponentManagerConfigurationHandler::$instance = new ComponentManagerConfigurationHandler();
        }
        return ComponentManagerConfigurationHandler::$instance;
    }

    public static function add(ComponentManagerConfigurationContract $managerComponent): void
    {
        ComponentManagerConfigurationHandler::instance()->managerComponents[] = $managerComponent;
    }

    public static function run(): void
    {
        foreach (ComponentManagerConfigurationHandler::instance()->managerComponents ?? [] as $managerComponent) {
            $managerComponent->handler();
        }
    }

    public static function loadedDefinitions(): array
    {
        $definitions = [];
        foreach (ComponentManagerConfigurationHandler::instance()->managerComponents ?? [] as $managerComponent) {
            $definitions[get_class($managerComponent)] = $managerComponent->loadedDefinitions();
        }
        return $definitions;
    }
}
