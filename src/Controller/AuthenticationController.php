<?php

namespace App\Controller;

use App\Application\Authentication\AuthenticationUserHandler;
use App\Application\Authentication\AuthenticationUserRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthenticationController extends AbstractController
{
    public function __construct(private readonly AuthenticationUserHandler $handler){

    }

    #[Route('/login', methods: ['POST'])]
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
                'accessToken' => $authResult->accessToken,
                'refreshToken' => $authResult->refreshToken,
            ],
            Response::HTTP_OK,
        );
    }
}
