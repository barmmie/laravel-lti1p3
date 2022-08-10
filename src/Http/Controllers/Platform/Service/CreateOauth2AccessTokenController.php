<?php

namespace Wien\LaravelLTI\Http\Controllers\Platform\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use OAT\Library\Lti1p3Core\Security\OAuth2\Generator\AccessTokenResponseGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;

class CreateOauth2AccessTokenController
{

    public function __construct(
        private HttpFoundationFactoryInterface $httpFoundationFactory,
        private HttpMessageFactoryInterface $psr7Factory,
        private AccessTokenResponseGenerator $generator,
        private LoggerInterface $logger
    ) {
    }


    public function __invoke(Request $request, string $keyChainIdentifier): Response
    {
        $psr7Response = $this->psr7Factory->createResponse(new Response());

        try {
            $psr7AuthenticationResponse = $this->generator->generate(
                $this->psr7Factory->createRequest($request),
                $psr7Response,
                $keyChainIdentifier
            );

            $this->logger->info('OAuth2AccessTokenCreationAction: access token generation success');

              return $this->parseResponse(
                  $this->httpFoundationFactory->createResponse($psr7AuthenticationResponse)
              );

        } catch (OAuthServerException $exception) {
            $this->logger->error(sprintf('OAuth2AccessTokenCreationAction: %s', $exception->getMessage()));

            return $this->parseResponse(
                $this->httpFoundationFactory->createResponse($exception->generateHttpResponse($psr7Response))
            );
        }
    }

    private function parseResponse(\Symfony\Component\HttpFoundation\Response $response): Response
    {
        return new Response($response->getContent(), $response->getStatusCode(), $response->headers->all());
    }

}
