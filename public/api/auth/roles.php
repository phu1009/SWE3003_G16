<?php

return [
  'guest' => [
    'product.fetch'
  ],
  'customer' => [
    'product.fetch',
    'prescription.upload',
    'order.place',
    'order.track'
  ],
  'pharmacist' => [
    'product.fetch',
    'prescription.review',
    'prescription.approve',
    'note.add',
    'order.create'
  ],
  'inventory_manager' => [
    'inventory.check',
    'inventory.update',
    'report.view'
  ],
  'admin' => [
    'product.fetch',
    'user.manage',
    'report.generate',
    'system.settings',
    'inventory.check',
    'inventory.update',
    'prescription.review',
    'prescription.approve',
    'note.add',
    'order.create',
    'order.track',
    'order.place',
    'prescription.upload',
    'product.save',
    'product.delete'
  ]
];
