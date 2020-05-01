<?php

return [
    'USD_TO_AED'  => 3.6725,  
    'timezone'    => 'Asia/Dubai',
    'currency'    => 'AED',
    'date_format' => 'Y-m-d',
    'time_format' => 'g:i A',
    'dateTime'    => 'Y-m-d h:i:s',
    'rows'        => 10,
    'OTP_expiry'  => 5,
    'VAT'         => 5,
    'delivery_charge'  => 105,
    'orderStatuses' => [
        0 => 'Pending',
        1 => 'Assigned',
        // Drived added items to invoice
        2 => 'Picked',
        3 => 'Dropped',
        4 => 'Payment Done'
    ],
    'orderStatus' => [
        'New Booking' => [
            '0' => 'Pending',
        ],
        'Active Booking' => [
            '1' => 'Assigned',
            '2' => 'Picked',
            '3' => 'Dropped'
        ],
        'Completed' => [
            '4' => 'Payment Done'
        ]
    ]
];