<?php

return [
    'dataProcessor' => JsonData::class,
//    'mongo' => [
//        'db' => 'DB_NAME'
//    ],
    'appNodesOnly' => true,
    'blockedNodes' => [],
    'allowedMethods' => [
        'states' => ['GET']
    ]
];
