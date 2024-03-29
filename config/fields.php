<?php

return [
  'createUser' => [
    'Login Information' => [
      'username' => [
        'display_name' => 'Driver ID',
        'col' => '6',
        'type' =>  'text',
      ],
      'password' => [
        'display_name' => 'Login Pin',
        'col' => '6',
        'type' =>  'number',
      ],
      'license_no' => [
        'display_name' => 'License Number',
        'col' => '6',
        'type' =>  'text',
      ],
      'vehicle_id' => [
        'display_name' => 'Vehicle',
        'col' => '6',
        'type' =>  'select',
      ],
    ],
    'Personal Information' => [
      'gender' => [
        'display_name' => 'Title',
        'col' => '2',
        'type' =>  'select',
      ],
      'fname' => [
        'display_name' => 'First Name',
        'col' => '5',
        'type' =>  'text',
      ],
      'lname' => [
        'display_name' => 'Last Name',
        'col' => '5',
        'type' =>  'text',
			],
			'phone' => [
        'display_name' => 'Phone',
        'col' => '4',
        'type' =>  'text',
      ],
      'email' => [
        'display_name' => 'Email Address',
        'col' => '4',
        'type' =>  'email',
      ],
      'dob' => [
        'display_name' => 'Date of Birth',
        'col' => '4',
        'type' =>  'date',
			],
			'country' => [
        'display_name' => 'Country',
        'col' => '4',
        'type' =>  'text',
      ],
    ],
  ],
  'createVehicle' => [
    'Vehicle Information' => [
      'vehicle_number' => [
        'display_name' => 'Vehicle Number',
        'col' => '6',
        'type' =>  'text',
      ],
      'brand' => [
        'display_name' => 'Brand',
        'col' => '6',
        'type' =>  'text',
      ],
      'description' => [
        'display_name' => 'Description',
        'col' => '12',
        'type' =>  'textarea',
      ],
    ],
  ],
  'createOrder' => [
    'Order Information' => [
      'customer_id' => [
        'display_name' => 'Select Customer',
        'col' => '4',
        'type' =>  'select',
      ],
      'type' => [
        'display_name' => 'Type',
        'col' => '4',
        'type' =>  'select',
      ],
      'pick_location' => [
        'display_name' => 'Pickup Location',
        'col' => '4',
        'type' =>  'number',
      ],
      'pick_datetime' => [
        'display_name' => 'Pickup Date & Time',
        'col' => '4',
        'type' =>  'datetime',
      ],
      'drop_location' => [
        'display_name' => 'Drop Location',
        'col' => '4',
        'type' =>  'number',
      ],
      'drop_datetime' => [
        'display_name' => 'Drop Date & Time',
        'col' => '4',
        'type' =>  'datetime',
      ],
      'price' => [
        'display_name' => 'Price',
        'col' => '4',
        'type' =>  'number',
      ],
      'vat_amount' => [
        'display_name' => 'VAT',
        'col' => '4',
        'type' =>  'number',
      ],
      'delivery_charge' => [
        'display_name' => 'Delivery Charge',
        'col' => '4',
        'type' =>  'number',
      ],
    ],
  ],
  'createService' => [
    'Information' => [
      'name' => [
        'display_name' => 'Service Name',
        'col' => '6',
        'type' =>  'text',
      ],
      'price' => [
        'display_name' => 'Service Price',
        'col' => '6',
        'type' =>  'number',
      ],
      'description' => [
        'display_name' => 'Description',
        'col' => '12',
        'type' =>  'textarea',
        'placeholder' => 'Write Brief Description',
      ],
    ],
  ],
  'createCategory' => [
    'Information' => [
      'name' => [
        'display_name' => 'Category Name',
        'col' => '12',
        'type' =>  'text',
      ],
      'description' => [
        'display_name' => 'Description',
        'col' => '12',
        'type' =>  'textarea',
        'placeholder' => 'Write Brief Description',
      ],
    ],
  ],
  'createItem' => [
    'Information' => [
      'category_id' => [
        'display_name' => 'Choose Category',
        'col' => '4',
        'type' =>  'select',
      ],
      'name' => [
        'display_name' => 'Item Name',
        'col' => '4',
        'type' =>  'text',
      ],
      'price' => [
        'display_name' => 'Price',
        'col' => '4',
        'type' =>  'number',
      ],
      'description' => [
        'display_name' => 'Description',
        'col' => '12',
        'type' =>  'textarea',
        'placeholder' => 'Write Brief Description',
      ],
    ],
  ],
];
