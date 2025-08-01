<?php

session_start();
// TEMP: inject user for testing
// if (!isset($_SESSION['user'])) {
//   $_SESSION['user'] = [
//     'name' => 'Dev Manager',
//     'roles' => ['admin']
//   ];
// }
header('Content-Type: application/json');

// default guest session
$user = $_SESSION['user'] ?? [
  'name' => 'Guest',
  'roles' => ['guest']
];

$permissionsMap = require __DIR__ . '/auth/roles.php';

$granted = [];
foreach ($user['roles'] as $role) {
    $granted = array_merge($granted, $permissionsMap[$role] ?? []);
}
$granted = array_values(array_unique($granted));

echo json_encode([
  'logged_in'   => isset($_SESSION['user']),
  'roles'       => $user['roles'],
  'user'        => $user['name'],
  'permissions' => $granted
]);
