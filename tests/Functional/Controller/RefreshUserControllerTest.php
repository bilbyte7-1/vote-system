<?php

namespace App\Tests\Functional\Controller;

use App\Application\Authentication\AuthenticationRefreshTokenHandler;
use App\Application\Authentication\AuthenticationResult;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RefreshUserControllerTest extends WebTestCase
{
    public function testItRefreshTokens()
    {
        $client = self::createClient();

        $authenticationResult = new AuthenticationResult('access-token', 'refresh-token');
        $handler = self::createMock(AuthenticationRefreshTokenHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($authenticationResult);

        self::getContainer()->set(
            AuthenticationRefreshTokenHandler::class,
            $handler,
        );

        $client->jsonRequest(
            method: 'POST',
            uri: '/users/refresh',
            parameters: [
                'refresh_token' => $authenticationResult->refreshToken,
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

        self::assertArrayHasKey('access_token', $response);
        self::assertNotEmpty($response['access_token']);
        self::assertArrayHasKey('refresh_token', $response);
        self::assertNotEmpty($response['refresh_token']);
    }
}
