<?php

return [
    'dataProcessor' => JsonData::class,
    'mongo' => [
        'db' => 'sportyseat'
    ],
    'appNodesOnly' => true,
    'blockedNodes' => [],
    'allowedMethods' => [
        'state' => ['GET'],
        'lga' => ['GET']
    ]
];
