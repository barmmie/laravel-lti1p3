<?php

namespace Wien\LaravelLTI\Http\Controllers\Jwks;

use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use Symfony\Component\HttpFoundation\JsonResponse;

class JwksController
{
    public function __construct(
        private JwksExporter $exporter)
    {
    }

    public function __invoke(string $keySetName): JsonResponse
    {
        return new JsonResponse($this->exporter->export($keySetName));
    }
}
