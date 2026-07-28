<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Service\CategoryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController
{
    private CategoryService $service;

    public function __construct()
    {
        $this->service = new CategoryService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $lang = $request->getQueryParams()['lang'] ?? 'en';

        $stm = $this->service->getAll($adminUserId, $lang);

        $response->getBody()->write(json_encode($stm->fetchAll()));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $lang = $request->getQueryParams()['lang'] ?? 'en';

        $stm = $this->service->getOne(
            $adminUserId,
            (int) $args['id'],
            $lang
        );

        $category = $stm->fetch();

        if (!$category) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($category));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
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

    /**
     * POST /categories/{id}
     */
    public function insertTranslations(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $body = $request->getParsedBody();

        if (
            !isset($body['translations']) ||
            !is_array($body['translations']) ||
            empty($body['translations'])
        ) {

            $response->getBody()->write(json_encode([
                'message' => 'The translations field is required.'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        foreach ($body['translations'] as $translation) {

            if (
                !isset($translation['lang_code']) ||
                !isset($translation['label'])
            ) {

                $response->getBody()->write(json_encode([
                    'message' => 'Each translation must contain lang_code and label.'
                ]));

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
        }

        $result = $this->service->insertTranslations(
            (int) $args['id'],
            $body['translations']
        );

        if (!$result['success']) {

            $response->getBody()->write(json_encode([
                'message' => $result['message']
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($result['status']);
        }

        return $response->withStatus(201);
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
}