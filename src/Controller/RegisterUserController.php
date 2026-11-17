<?php

namespace App\Controller;

use App\Application\Registration\RegisterUserHandler;
use App\Application\Registration\RegisterUserRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterUserController
{
    public function __construct(private RegisterUserHandler $handler)
    {
    }

    #[Route(
        '/users/register',
        name: 'users_register',
        methods: ['POST']
    )]
    public function __invoke(
        Request $request
    ): JsonResponse {
        $data = $request->toArray();

        $dto = new RegisterUserRequest(
            email: $data['email'],
            plainPassword: $data['plainPassword'],
        );

        $user = $this->handler->handle($dto);

        return new JsonResponse(
            [
                'id' => $user->getId()->getId(),
                'email' => $user->getEmail()->getEmail(),
            ],
            Response::HTTP_CREATED,
        );
    }
}
