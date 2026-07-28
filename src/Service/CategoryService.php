<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use PDO;
use PDOException;

class CategoryService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll($adminUserId, string $lang = 'en')
    {
        $query = "
            SELECT
                c.id,
                c.company_id,
                c.active,
                COALESCE(ct_lang.label, ct_en.label, c.title) AS title
            FROM category c

            LEFT JOIN category_translation ct_lang
                ON ct_lang.category_id = c.id
                AND ct_lang.lang_code = :lang

            LEFT JOIN category_translation ct_en
                ON ct_en.category_id = c.id
                AND ct_en.lang_code = 'en'

            WHERE (
                c.company_id = :companyId
                OR c.company_id IS NULL
            )
        ";

        $stm = $this->pdo->prepare($query);

        $stm->execute([
            ':companyId' => $this->getCompanyFromAdminUser($adminUserId),
            ':lang' => $lang
        ]);

        return $stm;
    }

    public function getOne($adminUserId, $categoryId, string $lang = 'en')
    {
        $query = "
            SELECT
                c.id,
                c.company_id,
                c.active,
                COALESCE(ct_lang.label, ct_en.label, c.title) AS title
            FROM category c

            LEFT JOIN category_translation ct_lang
                ON ct_lang.category_id = c.id
                AND ct_lang.lang_code = :lang

            LEFT JOIN category_translation ct_en
                ON ct_en.category_id = c.id
                AND ct_en.lang_code = 'en'

            WHERE c.active = 1
            AND c.id = :categoryId
            AND (
                c.company_id = :companyId
                OR c.company_id IS NULL
            )
        ";

        $stm = $this->pdo->prepare($query);

        $stm->execute([
            ':categoryId' => $categoryId,
            ':companyId' => $this->getCompanyFromAdminUser($adminUserId),
            ':lang' => $lang
        ]);

        return $stm;
    }

    public function getProductCategory($productId)
    {
        $query = "
            SELECT c.id
            FROM category c
            INNER JOIN product_category pc
                ON pc.cat_id = c.id
            WHERE pc.product_id = {$productId}
        ";

        $stm = $this->pdo->prepare($query);

        $stm->execute();

        return $stm;
    }

    public function insertTranslations(int $categoryId, array $translations): array
    {
        if (!$this->categoryExists($categoryId)) {
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Category not found.'
            ];
        }

        try {

            $this->pdo->beginTransaction();

            $stm = $this->pdo->prepare("
                INSERT INTO category_translation (
                    category_id,
                    lang_code,
                    label
                ) VALUES (
                    :category_id,
                    :lang_code,
                    :label
                )
            ");

            foreach ($translations as $translation) {

                if (
                    !isset($translation['lang_code']) ||
                    !isset($translation['label'])
                ) {
                    throw new \InvalidArgumentException(
                        'Each translation must contain lang_code and label.'
                    );
                }

                $langCode = strtolower(trim($translation['lang_code']));
                $label = trim($translation['label']);

                if ($langCode === '' || $label === '') {
                    throw new \InvalidArgumentException(
                        'Language code and label cannot be empty.'
                    );
                }

                $stm->execute([
                    ':category_id' => $categoryId,
                    ':lang_code' => $langCode,
                    ':label' => $label
                ]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'status' => 201,
                'message' => 'Translations created successfully.'
            ];

        } catch (\InvalidArgumentException $exception) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'success' => false,
                'status' => 400,
                'message' => $exception->getMessage()
            ];

        } catch (PDOException $exception) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if (str_contains($exception->getMessage(), 'UNIQUE')) {
                return [
                    'success' => false,
                    'status' => 409,
                    'message' => 'One or more translations already exist.'
                ];
            }

            return [
                'success' => false,
                'status' => 500,
                'message' => 'Unexpected error while saving translations.'
            ];
        }
    }

    public function insertOne($body, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            INSERT INTO category (
                company_id,
                title,
                active
            ) VALUES (
                {$this->getCompanyFromAdminUser($adminUserId)},
                '{$body['title']}',
                {$body['active']}
            )
        ");

        return $stm->execute();
    }

    public function updateOne($id, $body, $adminUserId)
    {
        $active = (int) $body['active'];

        $stm = $this->pdo->prepare("
            UPDATE category
            SET title = '{$body['title']}',
                active = {$active}
            WHERE id = {$id}
            AND company_id = {$this->getCompanyFromAdminUser($adminUserId)}
        ");

        return $stm->execute();
    }

    public function deleteOne($id, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            DELETE
            FROM category
            WHERE id = {$id}
            AND company_id = {$this->getCompanyFromAdminUser($adminUserId)}
        ");

        return $stm->execute();
    }

    private function categoryExists(int $categoryId): bool
    {
        $stm = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM category
            WHERE id = :id
        ");

        $stm->execute([
            ':id' => $categoryId
        ]);

        return (int) $stm->fetchColumn() > 0;
    }

    private function getCompanyFromAdminUser($adminUserId)
    {
        $query = "
            SELECT company_id
            FROM admin_user
            WHERE id = {$adminUserId}
        ";

        $stm = $this->pdo->prepare($query);

        $stm->execute();

        return $stm->fetch()->company_id;
    }
}