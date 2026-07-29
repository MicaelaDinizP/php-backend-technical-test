<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Model\Product;
use Contatoseguro\TesteBackend\Service\CategoryService;
use Contatoseguro\TesteBackend\Service\ProductService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductController
{
    private ProductService $service;
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->categoryService = new CategoryService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $filters = $request->getQueryParams();

        $stm = $this->service->getAll($adminUserId, $filters);

        $response->getBody()->write(json_encode($stm->fetchAll()));

        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stm = $this->service->getOne($args['id']);
        $product = Product::hydrateByFetch($stm->fetch());

        $adminUserId = $request->getHeader('admin_user_id')[0];

        $lang = $request->getQueryParams()['lang'] ?? 'en';

        $productCategories = $this->categoryService
            ->getProductCategory($product->id)
            ->fetchAll();

        $categories = [];

        foreach ($productCategories as $productCategory) {

            $category = $this->categoryService
                ->getOne(
                    $adminUserId,
                    $productCategory->id,
                    $lang
                )
                ->fetch();

            if ($category) {
                $categories[] = $category->title;
            }
        }

        $product->setCategories($categories);

        $response->getBody()->write(json_encode($product));

        return $response->withStatus(200);
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->insertOne($body, $adminUserId)) {
            return $response->withStatus(200);
        }

        return $response->withStatus(404);
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->updateOne($args['id'], $body, $adminUserId)) {
            return $response->withStatus(200);
        }

        return $response->withStatus(404);
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->deleteOne($args['id'], $adminUserId)) {
            return $response->withStatus(200);
        }

        return $response->withStatus(404);
    }

    public function getLastPriceChange(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $result = $this->service
        ->getLastPriceChange((int) $args['id'])
        ->fetch();

    if (!$result) {
        return $response->withStatus(404);
    }

    $response->getBody()->write(json_encode($result));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
}
}