<?php

declare(strict_types=1);

namespace Wien\LaravelLTI\Repository;

use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;

class RegistrationRepository implements RegistrationRepositoryInterface
{
    /** @var CollectionInterface|RegistrationInterface[] */
    private $registrations;

    public function __construct(array $registrations = [])
    {
        $this->registrations = new Collection();

        foreach ($registrations as $registration) {
            /** @param  RegistrationInterface  $registration */
            $this->registrations->set($registration->getIdentifier(), $registration);
        }
    }

    public function find(string $identifier): ?RegistrationInterface
    {
        return $this->registrations->get($identifier);
    }

    public function findAll(): array
    {
        return $this->registrations->all();
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        foreach ($this->registrations->all() as $registration) {
            if ($registration->getClientId() === $clientId) {
                return $registration;
            }
        }

        return null;
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        foreach ($this->registrations->all() as $registration) {
            if ($registration->getPlatform()->getAudience() === $issuer) {
                if (null !== $clientId) {
                    if ($registration->getClientId() === $clientId) {
                        return $registration;
                    }
                } else {
                    return $registration;
                }
            }
        }

        return null;
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        foreach ($this->registrations->all() as $registration) {
            if ($registration->getTool()->getAudience() === $issuer) {
                if (null !== $clientId) {
                    if ($registration->getClientId() === $clientId) {
                        return $registration;
                    }
                } else {
                    return $registration;
                }
            }
        }

        return null;
    }
}
