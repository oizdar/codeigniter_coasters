<?php

return [
    'time' => 'Time: {time}',
    'coaster' => [
        'header' => 'Coaster: {name}',
        'opening_hours' => 'Opening hours: {from} - {to}',
        'wagons' => 'Wagons: {wagons} / {expected}',
        'staff' => 'Staff: {staff} / {required_staff}',
        'clients' => 'Clients: {clients}',
        'status' => [
            'ok' => 'Status: OK',
            'problem' => 'Problem: ',
            'not_enough_staff' => 'Not enough staff: missing {below}',
            'too_much_staff' => 'Too much staff: {above} above assigned',
            'no_wagons' => 'No wagons',
            'too_many_wagons' => 'Too many wagons: {wagons} wagons',
        ],
    ],
    'redis' => [
        'error' => 'Error while saving data to Redis: {error}',
    ],
];
