<?php

return [
    'project_name' => env('PROJECT_NAME', 'Agroptix'),
    
    'type' => [
        'perishable' => 'Perishable',
        'non_perishable' => 'Non Perishable'
    ],
    
    'source_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
    ],
    
    'production_methods' => [
        'Natural' => 'Natural',
        'Organic' => 'Organic',
        'Mixed' => 'Mixed'
    ]
];
