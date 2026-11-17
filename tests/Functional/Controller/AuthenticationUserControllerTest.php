<?php

namespace App\Tests\Functional\Controller;

use App\Application\Authentication\AuthenticationResult;
use App\Application\Authentication\AuthenticationUserHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationUserControllerTest extends WebTestCase
{
    public function testItAuthenticationUser(): void
    {
        $client = self::createClient();

        $handlerStub = $this->createStub(AuthenticationUserHandler::class);
        $handlerStub
            ->method('handle')
            ->willReturn(
                new AuthenticationResult(
                    'access_token',
                    'refresh_token',
                )
            );

        self::getContainer()->set(
            AuthenticationUserHandler::class,
            $handlerStub
        );

        $client->jsonRequest(
            method: 'POST',
            uri: '/login',
            parameters: [
                'email' => 'user@example.com',
                'password' => 'StrongPassword123!',
            ],
        );

        self::assertResponseStatusCodeSame(
            Response::HTTP_OK,
        );

        $content = $client
            ->getResponse()
            ->getContent();

        $response = json_decode(
            $content,
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        self::assertSame(
            [
                'access_token' => 'access_token',
                'refresh_token' => 'refresh_token',
            ],
            $response,
        );
    }
}
