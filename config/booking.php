<?php

return [
    // Each court allows only 1 booking per slot
    'max_per_slot' => 1,

    'courts' => [
        'Court 1 (Rubber Mat)'  => ['rate' => 25.00],
        'Court 2 (Rubber Mat)'  => ['rate' => 25.00],
        'Court 3 (Parquet Wood)' => ['rate' => 35.00],
        'Court 4 (VIP Covered)'  => ['rate' => 45.00],
    ],

    'slots' => [
        '09:00',
        '10:00',
        '11:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
        '21:00',
    ],
];