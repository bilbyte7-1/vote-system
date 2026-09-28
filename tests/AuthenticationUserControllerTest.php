<?php


namespace App\Tests;

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

        self::assertArrayHasKey('accessToken', $response);
        self::assertSame('access_token', $response['accessToken']);
        self::assertArrayHasKey('refreshToken', $response);
        self::assertSame('refresh_token', $response['refreshToken']);
    }
}
