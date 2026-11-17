<?php

namespace App\Controller;

use App\Application\Authentication\AuthenticationRefreshTokenHandler;
use App\Application\Authentication\RefreshTokenRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RefreshUserController
{
    #[Route('/users/refresh', methods: ['POST'])]
    public function __invoke(
        Request $request,
        AuthenticationRefreshTokenHandler $handler,
    ): JsonResponse {
        $data = $request->toArray();

        $result = $handler->handle(
            new RefreshTokenRequest(
                refreshToken: $data['refresh_token']
            )
        );

        return new JsonResponse(
            [
                'access_token' => $result->accessToken,
                'refresh_token' => $result->refreshToken,
            ],
            Response::HTTP_OK,
        );
    }
}
