<?php
require_once __DIR__ . '/../../lib/Database.php';

class ProductModel {

    public static function all(): array
    {
        return Database::get()
            ->query("SELECT * FROM products WHERE is_active = 1 ORDER BY product_name")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $st = Database::get()
              ->prepare("SELECT * FROM products WHERE product_id = ? AND is_active = 1");
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public static function create(array $d): int {
        $sql="INSERT INTO products
                (sku,product_name,description,unit_price,is_rx_only,image_path)
              VALUES (?,?,?,?,?,?)";
        Database::get()->prepare($sql)->execute([
            $d['sku'],$d['product_name'],$d['description'],
            $d['unit_price'],$d['is_rx_only'],$d['image_path']
        ]);
        return (int)Database::get()->lastInsertId();
    }

    public static function update(int $id,array $d): bool {
        $sql="UPDATE products SET sku=?,product_name=?,description=?,
              unit_price=?,is_rx_only=?,image_path=? WHERE product_id=?";
        return Database::get()->prepare($sql)->execute([
            $d['sku'],$d['product_name'],$d['description'],
            $d['unit_price'],$d['is_rx_only'],$d['image_path'],$id
        ]);
    }

    public static function delete(int $id): bool
    {
        return Database::get()
            ->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?")
            ->execute([$id]);
    }
}
