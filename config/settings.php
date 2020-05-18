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
    ],
    'orderStatuses' => [
        0 => 'New',
        1 => 'Assigned',
        2 => 'Start Trip',
        3 => 'Arrived at Customer',
        4 => 'Start Trip for Destination',
        5 => 'Reached Destination',
        6 => 'Payment Done and Closed'
    ],
    'customer_active_booking_statuses' => [0,1,2,3,4,5],
    'driver_active_booking_statuses' => [2,3,4,5],
];