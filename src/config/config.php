<?php

return [

    /** the name of the redis node set */
    'nodeSetName' => '',

    'cluster' => false,

    /** Array of sentinels */
    'masters' => [
        [
            'host' => '',
            'port' => '',
        ]
    ]
];
