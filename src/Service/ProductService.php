<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;

class ProductService
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll($adminUserId, array $filters = [])
{
    $query = "
        SELECT
            p.*,
            c.title AS category
        FROM product p
        INNER JOIN product_category pc
            ON pc.product_id = p.id
        INNER JOIN category c
            ON c.id = pc.cat_id
        WHERE p.company_id = :companyId
    ";

    $params = [
        ':companyId' => (int) $adminUserId,
    ];

    if (isset($filters['active'])) {
        $query .= " AND p.active = :active";
        $params[':active'] = (int) $filters['active'];
    }

    if (isset($filters['category'])) {
        $query .= " AND c.id = :category";
        $params[':category'] = (int) $filters['category'];
    }

    if (isset($filters['min_stock'])) {
        $query .= " AND p.stock >= :min_stock";
        $params[':min_stock'] = (int) $filters['min_stock'];
    }

    if (isset($filters['sort'])) {
        if ($filters['sort'] === 'asc') {
            $query .= " ORDER BY p.created_at ASC, p.id ASC";
        }

        if ($filters['sort'] === 'desc') {
            $query .= " ORDER BY p.created_at DESC, p.id DESC";
        }
    }

    $stm = $this->pdo->prepare($query);
    $stm->execute($params);

    return $stm;
}
    public function getOne($id)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM product
            WHERE id = :id
        ");

        $stm->execute([
            ':id' => (int) $id,
        ]);

        return $stm;
    }

       public function insertOne($body, $adminUserId)
{
    try {
        $this->pdo->beginTransaction();

        $stock = (int) ($body['stock'] ?? 0);

        $stm = $this->pdo->prepare("
            INSERT INTO product (
                company_id,
                title,
                price,
                active,
                stock
            ) VALUES (
                :company_id,
                :title,
                :price,
                :active,
                :stock
            )
        ");

        if (!$stm->execute([
            ':company_id' => (int) $body['company_id'],
            ':title' => (string) $body['title'],
            ':price' => (float) $body['price'],
            ':active' => (int) $body['active'],
            ':stock' => $stock,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $productId = (int) $this->pdo->lastInsertId();

        $stm = $this->pdo->prepare("
            INSERT INTO product_category (
                product_id,
                cat_id
            ) VALUES (
                :product_id,
                :cat_id
            )
        ");

        if (!$stm->execute([
            ':product_id' => $productId,
            ':cat_id' => (int) $body['category_id'],
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                action,
                changed_field,
                old_value
            ) VALUES (
                :product_id,
                :admin_user_id,
                :action,
                :changed_field,
                :old_value
            )
        ");

        if (!$stm->execute([
            ':product_id' => $productId,
            ':admin_user_id' => (int) $adminUserId,
            ':action' => 'create',
            ':changed_field' => null,
            ':old_value' => null,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $this->pdo->commit();

        return true;

    } catch (\Throwable $e) {

        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        return false;
    }
}
    public function updateOne($id, $body, $adminUserId)
{
    try {
        $this->pdo->beginTransaction();

        $currentProduct = $this->getCurrentProduct((int) $id);

        if (!$currentProduct) {
            $this->pdo->rollBack();
            return false;
        }

        $priceChanged = (float) $currentProduct->price !== (float) $body['price'];

        $stock = (int) ($body['stock'] ?? 0);

        $stm = $this->pdo->prepare("
            UPDATE product
            SET
                company_id = :company_id,
                title = :title,
                price = :price,
                active = :active,
                stock = :stock
            WHERE id = :id
        ");

        if (!$stm->execute([
            ':company_id' => (int) $body['company_id'],
            ':title' => (string) $body['title'],
            ':price' => (float) $body['price'],
            ':active' => (int) $body['active'],
            ':stock' => $stock,
            ':id' => (int) $id,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $stm = $this->pdo->prepare("
            UPDATE product_category
            SET cat_id = :category_id
            WHERE product_id = :product_id
        ");

        if (!$stm->execute([
            ':category_id' => (int) $body['category_id'],
            ':product_id' => (int) $id,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $changedField = null;
        $oldValue = null;

        if ($priceChanged) {
            $changedField = 'price';
            $oldValue = (string) $currentProduct->price;
        }

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                action,
                changed_field,
                old_value
            ) VALUES (
                :product_id,
                :admin_user_id,
                :action,
                :changed_field,
                :old_value
            )
        ");

        if (!$stm->execute([
            ':product_id' => (int) $id,
            ':admin_user_id' => (int) $adminUserId,
            ':action' => 'update',
            ':changed_field' => $changedField,
            ':old_value' => $oldValue,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $this->pdo->commit();

        return true;

    } catch (\Throwable $e) {

        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        return false;
    }
}
 
    public function deleteOne($id, $adminUserId)
{
    try {
        $this->pdo->beginTransaction();

        $stm = $this->pdo->prepare("
            DELETE FROM product_category
            WHERE product_id = :product_id
        ");

        if (!$stm->execute([
            ':product_id' => (int) $id,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $stm = $this->pdo->prepare("
            DELETE FROM product
            WHERE id = :id
        ");

        if (!$stm->execute([
            ':id' => (int) $id,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                action,
                changed_field,
                old_value
            ) VALUES (
                :product_id,
                :admin_user_id,
                :action,
                :changed_field,
                :old_value
            )
        ");

        if (!$stm->execute([
            ':product_id' => (int) $id,
            ':admin_user_id' => (int) $adminUserId,
            ':action' => 'delete',
            ':changed_field' => null,
            ':old_value' => null,
        ])) {
            $this->pdo->rollBack();
            return false;
        }

        $this->pdo->commit();

        return true;

    } catch (\Throwable $e) {

        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        return false;
    }
}

    public function getLog($id)
    {
        $stm = $this->pdo->prepare("
            SELECT
                au.name,
                pl.action,
                pl.timestamp
            FROM product_log pl
            INNER JOIN admin_user au
                ON au.id = pl.admin_user_id
            WHERE pl.product_id = :product_id
            ORDER BY pl.timestamp
        ");

        $stm->execute([
            ':product_id' => (int) $id,
        ]);

        return $stm;
    }
   private function getCurrentProduct(int $id): object|false
{
    $stm = $this->pdo->prepare("
        SELECT id, price
        FROM product
        WHERE id = :id
    ");

    $stm->execute([
        ':id' => $id,
    ]);

    return $stm->fetch();
}
public function getLastPriceChange(int $productId)
{
    $stm = $this->pdo->prepare("
        SELECT
            au.name,
            pl.timestamp
        FROM product_log pl
        INNER JOIN admin_user au
            ON au.id = pl.admin_user_id
        WHERE
            pl.product_id = :product_id
            AND pl.changed_field = 'price'
        ORDER BY pl.timestamp DESC
        LIMIT 1
    ");

    $stm->execute([
        ':product_id' => $productId,
    ]);

    return $stm;
}
}
