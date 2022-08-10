<?php

namespace Wien\LaravelLTI\Http\Controllers\Platform\Message;

use Illuminate\Http\Request;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcAuthenticator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OidcAuthenticationController
{
    public function __construct(
        private HttpMessageFactoryInterface $factory,
        private OidcAuthenticator $authenticator,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $launchRequest = $this->authenticator->authenticate($this->factory->createRequest($request));

            $this->logger->info('OidcAuthenticationAction: authentication success');

            return new Response($launchRequest->toHtmlRedirectForm());
        } catch (LtiExceptionInterface $exception) {
            $this->logger->error(sprintf('OidcAuthenticationAction: %s', $exception->getMessage()));

            return new Response(
                $this->convertThrowableMessageToHtml($exception),
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    private function convertThrowableMessageToHtml(Throwable $throwable): string
    {
        return htmlspecialchars($throwable->getMessage(), ENT_QUOTES);
    }
}
