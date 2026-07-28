<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Service\CommentService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Contatoseguro\TesteBackend\Service\ServiceResult;

class CommentController
{
    private CommentService $service;

    public function __construct()
    {
        $this->service = new CommentService();
    }

    public function create(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $body = $request->getParsedBody();

    $adminUserId = (int) $request->getHeader('admin_user_id')[0];

    $result = $this->service->create(
        (int) $args['productId'],
        $body,
        $adminUserId
    );

    return $this->buildResponse($response, $result);
}

    public function reply(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $body = $request->getParsedBody();

    $adminUserId = (int) $request->getHeader('admin_user_id')[0];

    $result = $this->service->reply(
        (int) $args['productId'],
        (int) $args['commentId'],
        $body,
        $adminUserId
    );

    return $this->buildResponse($response, $result);
}

    public function delete(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $adminUserId = (int) $request->getHeader('admin_user_id')[0];

        $result = $this->service->delete(
            (int) $args['commentId'],
            $adminUserId
        );

        return $this->buildResponse($response, $result);
    }

    public function like(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $adminUserId = (int) $request->getHeader('admin_user_id')[0];

        $result = $this->service->like(
            (int) $args['commentId'],
            $adminUserId
        );

        return $this->buildResponse($response, $result);
    }

    public function list(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $result = $this->service->getProductComments(
            (int) $args['productId']
        );

        return $this->buildResponse($response, $result);
    }

    private function buildResponse(
    ResponseInterface $response,
    ServiceResult $result
): ResponseInterface {

    $response->getBody()->write(json_encode([
        'success' => $result->success,
        'message' => $result->message,
        'errors' => $result->errors,
        'data' => $result->data
    ]));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($result->status);
}
}