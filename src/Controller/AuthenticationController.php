<?php

namespace App\Controller;

use App\Application\Authentication\AuthenticationUserHandler;
use App\Application\Authentication\AuthenticationUserRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthenticationController
{
    public function __construct(private readonly AuthenticationUserHandler $handler)
    {
    }

    #[Route(
        '/login',
        name: 'users_login',
        methods: ['POST']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $dto = new AuthenticationUserRequest(
            email: $data['email'],
            plainPassword: $data['password'],
        );

        $authResult = $this->handler->handle($dto);

        return new JsonResponse(
            [
                'access_token' => $authResult->accessToken,
                'refresh_token' => $authResult->refreshToken,
            ],
            Response::HTTP_OK,
        );
    }
}
