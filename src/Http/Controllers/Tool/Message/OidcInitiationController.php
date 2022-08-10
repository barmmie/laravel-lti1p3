<?php

namespace Wien\LaravelLTI\Http\Controllers\Tool\Message;

use Illuminate\Http\Request;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OidcInitiationController
{

    public function __construct(
        private HttpMessageFactoryInterface $factory,
        private OidcInitiator $initiator,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $oidcAuthenticationRequest = $this->initiator->initiate($this->factory->createRequest($request));

            $this->logger->info('OidcInitiationAction: initiation success');

            return new RedirectResponse($oidcAuthenticationRequest->toUrl());

        } catch (LtiExceptionInterface $exception) {
            $this->logger->error(sprintf('OidcInitiationAction: %s', $exception->getMessage()));

            return new Response(
                $this->convertThrowableMessageToHtml($exception),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    private function convertThrowableMessageToHtml(Throwable $throwable): string
    {
        return htmlspecialchars($throwable->getMessage(), ENT_QUOTES);
    }
}
