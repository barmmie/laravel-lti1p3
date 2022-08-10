<?php

// config for Wien/LaravelLti1p3
return [
    'routing' => [
        'domain' => null,
        'prefix' => null
    ],
    'key_chains' => [
        'platformKey' => [
            'key_set_name' => 'platformSet',
            'public_key' => base_path('/config/secrets/dev/public.key'),
            'private_key' => base_path('/config/secrets/dev/private.key'),
            'private_key_passphrase' => NULL,
        ],
        'toolKey' => [
            'key_set_name' => 'toolSet',
            'public_key' => base_path('/config/secrets/dev/public.key'),
            'private_key' => base_path('/config/secrets/dev/private.key'),
            'private_key_passphrase' => NULL,
        ],
        'paidiKey' => [
            'key_set_name' => 'paidiSet',
            'public_key' => base_path('/config/secrets/dev/public.key'),
            'private_key' => base_path('/config/secrets/dev/private.key'),
            'private_key_passphrase' => NULL,
            'algorithm' => 'RS256',
        ],
    ],
    'platforms' => [
        'localPlatform' => [
            'name' => 'Local platform',
            'audience' => 'http://localhost/platform',
            'oidc_authentication_url' => 'http://localhost/lti1p3/oidc/authentication',
            'oauth2_access_token_url' => 'http://localhost/lti1p3/auth/platformKey/token',
        ],
        'devkit_paidi_1' => [
            'name' => 'DevKit paidi_1',
            'audience' => 'http://kogneos-lti-test.kogneos.com',
            'oidc_authentication_url' => 'https://kogneos-lti-test.kogneos.com/lti1p3/oidc/authentication',
            'oauth2_access_token_url' => 'https://kogneos-lti-test.kogneos.com/lti1p3/auth/platformKey/token',
        ],
    ],
    'tools' => [
        'localTool' => [
            'name' => 'Local tool',
            'audience' => 'http://localhost/tool',
            'oidc_initiation_url' => 'http://localhost/lti1p3/oidc/initiation',
            'launch_url' => NULL,
            'deep_linking_url' => NULL,
        ],
        'riTool' => [
            'name' => 'Qwiklabs_1',
            'audience' => 'http://kogneos-lti-test.kogneos.com',
            'oidc_initiation_url' => 'https://kogneos-lti-test.kogneos.com/lti1p3/oidc/initiation',
            'launch_url' => 'https://kogneos-lti-test.kogneos.com/tool/launch',
            'deep_linking_url' => NULL,
        ],
        'tao_devkit_tool' => [
            'name' => 'TAO DevKit tool',
            'audience' => 'http://devkit-lti1p3.test:8000/tool',
            'oidc_initiation_url' => 'http://devkit-lti1p3.test:8000/lti1p3/oidc/initiation',
            'launch_url' => 'http://devkit-lti1p3.test:8000/tool/launch',
            'deep_linking_url' => 'http://devkit-lti1p3.test:8000/tool/launch',
        ],
    ],
    'registrations' => [
        'local' => [
            'client_id' => 'client_id',
            'platform' => 'localPlatform',
            'tool' => 'localTool',
            'deployment_ids' => [
                0 => 'deploymentId1',
            ],
            'platform_key_chain' => 'platformKey',
            'tool_key_chain' => 'toolKey',
            'platform_jwks_url' => NULL,
            'tool_jwks_url' => NULL,
        ],
        'devkit_paidi_1' => [
            'client_id' => 'QW12345',
            'platform' => 'devkit_paidi_1',
            'tool' => 'kogneos_tool',
            'deployment_ids' => [
                0 => 'SS_2',
            ],
            'platform_key_chain' => 'platformKey',
            'tool_key_chain' => 'toolKey',
            'platform_jwks_url' => 'https://kogneos-lti-test.kogneos.com/lti1p3/.well-known/jwks/platformSet.json',
            'tool_jwks_url' => 'https://kogneos-lti-test.kogneos.com/lti1p3/.well-known/jwks/toolSet.json',
            'order' => 1,
        ],
        'kogneos_platform_id' => [
            'client_id' => 'kogneos_platform_client_id',
            'platform' => 'kogneosPlatform',
            'tool' => 'tao_devkit_tool',
            'deployment_ids' => [
                0 => 'deploymentId1',
                1 => 'deploymentId2',
            ],
            'platform_key_chain' => 'kogneos_platform_Key',
            'tool_key_chain' => 'toolKey',
            'platform_jwks_url' => 'http://kogneos.test/lti1p3/.well-known/jwks/platformSet.json',
            'tool_jwks_url' => 'http://kogneos.test/lti1p3/.well-known/jwks/toolSet.json',
            'order' => 1,
        ],
    ],
];
