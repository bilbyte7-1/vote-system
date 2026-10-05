<?php

namespace App\Controller;

use App\Application\VoteSessionStart\VoteSessionStartHandler;
use App\Application\VoteSessionStart\VoteSessionStartRequest;
use App\Infrastructure\Security\SymfonySecurityUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class VoteSessionStartController
{
    #[Route(
        '/vote-sessions',
        name: 'vote_sessions_start',
        methods: ['POST']
    )]
    public function __invoke(
        #[CurrentUser] SymfonySecurityUser $user,
        VoteSessionStartHandler $voteSessionHandler,
        Request                 $request,
    ): Response {

        $data = $request->toArray();
        $voteSessionStartRequest = new VoteSessionStartRequest(
            $user->getUserIdentifier(),
            $data['first_phase_duration'],
            $data['second_phase_duration']
        );

        $voteSessionId = $voteSessionHandler->handle($voteSessionStartRequest);

        return new Response(
            content: null,
            status: Response::HTTP_CREATED,
            headers: [
                'Location' => '/vote-sessions/' . $voteSessionId->getId(),
            ],
        );
    }
}
