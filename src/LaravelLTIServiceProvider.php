<?php

namespace Wien\LaravelLTI;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use OAT\Library\Lti1p3Core\Platform\PlatformFactory;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationFactory;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactory;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;
use OAT\Library\Lti1p3Core\Tool\ToolFactory;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use Wien\LaravelLTI\Repository\RegistrationRepository;

class LaravelLTIServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-lti.php', 'laravel-lti'
        );

        $this->registerLTIRegistrations();
        $this->registerKeyChainRepositories();
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        });
    }

    private function registerLTIRegistrations()
    {
        $this->app->bind(RegistrationRepositoryInterface::class, function ($app) {
            $configuration = config('laravel-lti');

            return new RegistrationRepository(
                $this->buildRegistrations(
                    $configuration,
                    $this->buildKeyChains($configuration),
                    $this->buildPlatforms($configuration),
                    $this->buildTools($configuration)
                )
            );
        });
    }

    private function registerKeyChainRepositories()
    {
        $this->app->bind(KeyChainRepositoryInterface::class, function ($app) {
            $repository = new KeyChainRepository();
            $configuration = config('laravel-lti');

            $keychains = $this->buildKeyChains($configuration);

            foreach ($keychains as $keychain) {
                $repository->addKeyChain($keychain);
            }

            return $repository;
        });
    }

    private function routeConfiguration(): array
    {
        return [
            'domain' => config('laravel-lti.routing.domain', null),
            'prefix' => config('laravel-lti.routing.prefix'),
        ];
    }

    /**
     * @return KeyChainInterface[]
     */
    private function buildKeyChains(array $configuration): array
    {
        $keyChains = [];
        $keyChainFactory = new KeyChainFactory();
        foreach ($configuration['key_chains'] ?? [] as $keyId => $keyData) {
            $keyChain = $keyChainFactory->create(
                $keyId,
                $keyData['key_set_name'],
                $keyData['public_key'],
                $keyData['private_key'] ?? null,
                $keyData['private_key_passphrase'] ?? null,
                $keyData['algorithm'] ?? KeyInterface::ALG_RS256
            );

            $keyChains[$keyId] = $keyChain;
        }

        return $keyChains;
    }

    /**
     * @return PlatformInterface[]
     */
    private function buildPlatforms(array $configuration): array
    {
        $platforms = [];
        $platformFactory = new PlatformFactory();

        foreach ($configuration['platforms'] ?? [] as $platformId => $platformData) {
            $platform = $platformFactory->create(
                $platformId,
                $platformData['name'],
                $platformData['audience'],
                $platformData['oidc_authentication_url'] ?? null,
                $platformData['oauth2_access_token_url'] ?? null
            );

            $platforms[$platformId] = $platform;
        }

        return $platforms;
    }

    /**
     * @return ToolInterface[]
     */
    private function buildTools(array $configuration): array
    {
        $tools = [];
        $toolFactory = new ToolFactory();

        foreach ($configuration['tools'] as $toolId => $toolData) {
            $tool = $toolFactory->create(
                $toolId,
                $toolData['name'],
                $toolData['audience'],
                $toolData['oidc_initiation_url'],
                $toolData['launch_url'] ?? null,
                $toolData['deep_linking_url'] ?? null
            );

            $tools[$toolId] = $tool;
        }

        return $tools;
    }

    /**
     * @return RegistrationInterface[]
     */
    private function buildRegistrations($configuration, $keyChains, $platforms, $tools): array
    {
        $registrations = [];
        $registrationFactory = new RegistrationFactory();

        $configuredRegistrations = $configuration['registrations'] ?? [];

        uasort($configuredRegistrations, function (array $registration1, array $registration2) {
            $orderRegistration1 = $registration1['order'] ?? 999999;
            $orderRegistration2 = $registration2['order'] ?? 999999;

            return $orderRegistration1 <=> $orderRegistration2;
        });

        foreach ($configuredRegistrations as $registrationId => $registrationData) {
            if (! array_key_exists($registrationData['platform'], $platforms)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Platform %s is not defined, possible values: %s',
                        $registrationData['platform'],
                        implode(', ', array_keys($platforms))
                    )
                );
            }

            if (! array_key_exists($registrationData['tool'], $tools)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Tool %s is not defined, possible values: %s',
                        $registrationData['tool'],
                        implode(', ', array_keys($tools))
                    )
                );
            }

            if (
                isset($registrationData['platform_key_chain'])
                && ! array_key_exists($registrationData['platform_key_chain'], $keyChains)
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Platform key chain %s is not defined, possible values: %s',
                        $registrationData['platform_key_chain'],
                        implode(', ', array_keys($keyChains))
                    )
                );
            }

            if (
                isset($registrationData['tool_key_chain'])
                && ! array_key_exists($registrationData['tool_key_chain'], $keyChains)
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Tool key chain %s is not defined, possible values: %s',
                        $registrationData['tool_key_chain'],
                        implode(', ', array_keys($keyChains))
                    )
                );
            }

            $registration = $registrationFactory->create(
                $registrationId,
                $registrationData['client_id'],
                $platforms[$registrationData['platform']],
                $tools[$registrationData['tool']],
                $registrationData['deployment_ids'] ?? [],
                $keyChains[$registrationData['platform_key_chain']] ?? null,
                $keyChains[$registrationData['tool_key_chain']] ?? null,
                $registrationData['platform_jwks_url'] ?? null,
                $registrationData['tool_jwks_url'] ?? null
            );

            $registrations[$registrationId] = $registration;
        }

        return $registrations;
    }
}
