<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class RegisterUserControllerTest extends WebTestCase
{
    public function testItRegistersUser(): void
    {
        $client = self::createClient();

        $email = sprintf(
            'user-%s@example.com',
            bin2hex(random_bytes(8)),
        );

        $client->jsonRequest(
            method: 'POST',
            uri: '/users/register',
            parameters: [
                'email' => $email,
                'plainPassword' => 'StrongPassword123!',
                'confirmPassword' => 'StrongPassword123!',
            ],
        );

        self::assertResponseStatusCodeSame(
            Response::HTTP_CREATED,
        );

        $content = $client
            ->getResponse()
            ->getContent();

        /** @var array{id: mixed, email: string} $response */
        $response = json_decode(
            $content,
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        self::assertArrayHasKey('id', $response);
        self::assertNotEmpty($response['id']);

        self::assertSame(
            $email,
            $response['email'],
        );
    }
}
