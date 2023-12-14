<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Define which configuration should be used
    |--------------------------------------------------------------------------
    */

    'use' => env('AMQP_RECEIVER_SERVICE', 'rabbitmq'),

    /*
    |--------------------------------------------------------------------------
    | AMQP properties separated by key
    |--------------------------------------------------------------------------
    */

    'properties' => [

        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'api_uri' => env('RABBITMQ_SERVER_API_URI', 'localhost:15672'),
            'host' => env('RABBITMQ_SERVER_HOST', 'localhost'),
            'port' => env('RABBITMQ_SERVER_PORT', 5672),
            'username' => env('RABBITMQ_SERVER_USER', 'admin'),
            'password' => env('RABBITMQ_SERVER_PASSWORD', 'admin'),
            'vhost' => '/',
            'connect_options' => [],
            'ssl_options' => [],

            'exchange' => 'amq.topic',
            'exchange_type' => 'topic',
            'exchange_passive' => false,
            'exchange_durable' => true,
            'exchange_auto_delete' => false,
            'exchange_internal' => false,
            'exchange_nowait' => false,
            'exchange_properties' => [],

            'queue_force_declare' => false,
            'queue_passive' => false,
            'queue_durable' => true,
            'queue_exclusive' => false,
            'queue_auto_delete' => false,
            'queue_nowait' => false,
            'queue_properties' => ['x-ha-policy' => ['S', 'all']],

            'consumer_tag' => '',
            'consumer_no_local' => false,
            'consumer_no_ack' => false,
            'consumer_exclusive' => false,
            'consumer_nowait' => false,
            'timeout' => 0,
            'persistent' => false,

            'qos' => false,
            'qos_prefetch_size' => 0,
            'qos_prefetch_count' => 1,
            'qos_a_global' => false,

            'exchanges' => [
                [
                    'name' => '{exchange}',
                    'vhost' => '/',
                    'is_default' => true,
                    'type' => 'topic',
                    'durable' => true,
                    'auto_delete' => false,
                    'internal' => false,
                    'arguments' => [],
                    'use_in' => [
                        // {Class}Publisher
                    ]
                ],
                [
                    'name' => 'dlx.{exchange}',
                    'vhost' => '/',
                    'is_default' => true,
                    'type' => 'fanout',
                    'durable' => true,
                    'auto_delete' => false,
                    'internal' => false,
                    'arguments' => []
                ]
            ],
            'queues' => [
                [
                    'name' => 'default',
                    'is_default' => true,
                    'vhost' => '/',
                    'durable' => true,
                    'auto_delete' => false,
                    'arguments' => []
                ]
            ],
            'bindings' => [
                [
                    'source' => '{exchange}',
                    'routing_key' => [
                        'pattern' => '{param1}.*.{param2}'
                    ],
                    'is_default' => true,
                    'vhost' => '/',
                    'destination' => null,
                    'destination_type' => 'queue',
                    'arguments' => []
                ]
            ],

            'definitions' => [
                'resources' => [
                    [
                        'name' => 'Resource Name',
                        'consumers' => [
                            [
                                'tenant' => 'NAME_TENANT'
                            ]
                        ],
                        'enabled' => false,
                        'events' => [
                            [
                                'enabled' => false,
                                'event' => 'event.name'
                            ]
                        ]
                    ]
                ]
            ]
        ],

    ],

];
