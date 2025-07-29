<?php


function require_permission(string $permission) {
  session_start();

  if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
  }

  $roles = $_SESSION['user']['roles'] ?? [];
  $allRoles = require __DIR__ . '/../public/api/auth/roles.php';

  $userPermissions = [];
  foreach ($roles as $role) {
    $userPermissions = array_merge($userPermissions, $allRoles[$role] ?? []);
  }

  if (!in_array($permission, array_unique($userPermissions))) {
    http_response_code(403);
    echo json_encode(['error' => 'Permission denied']);
    exit;
  }
}