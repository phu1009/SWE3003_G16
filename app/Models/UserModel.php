<?php
require_once __DIR__ . '/../../lib/Database.php';

class UserModel {

    public static function createCustomer(string $email, string $password, string $fullName): bool {
        $pdo = Database::get();
        $pdo->beginTransaction();
        try {
            // 1) insert user
            $stmt = $pdo->prepare(
                "INSERT INTO users (email, password_hash, full_name) VALUES (?,?,?)"
            );
            $stmt->execute([$email, password_hash($password, PASSWORD_BCRYPT), $fullName]);
            $uid = (int)$pdo->lastInsertId();

            // 2) add to customers table (same id)
            $stmt = $pdo->prepare("INSERT INTO customers (customer_id) VALUES (?)");
            $stmt->execute([$uid]);

            // 3) assign 'customer' role
            $roleId = self::getRoleIdByName('customer');
            $stmt   = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?,?)");
            $stmt->execute([$uid, $roleId]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public static function findByEmail(string $email): ?array {
        $sql = "SELECT u.*, GROUP_CONCAT(r.role_name) AS roles
                FROM users u
                LEFT JOIN user_roles ur ON u.user_id = ur.user_id
                LEFT JOIN roles r       ON ur.role_id = r.role_id
                WHERE u.email = ?
                GROUP BY u.user_id";
        $stmt = Database::get()->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getRoleIdByName(string $roleName): int {
        $stmt = Database::get()->prepare("SELECT role_id FROM roles WHERE role_name=?");
        $stmt->execute([$roleName]);
        $id = $stmt->fetchColumn();
        if (!$id) throw new RuntimeException("Role $roleName not found");
        return (int)$id;
    }
}
