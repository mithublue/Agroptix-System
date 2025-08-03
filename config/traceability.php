<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Blockchain-Style Hashing
    |--------------------------------------------------------------------------
    |
    | This option enables blockchain-style hashing for trace events, where each
    | event's hash depends on the previous event's hash, creating an immutable
    | chain of events. Disable this if you don't need blockchain-style integrity.
    |
    */
    'enable_blockchain_hashing' => env('TRACEABILITY_ENABLE_BLOCKCHAIN', true),

    /*
    |--------------------------------------------------------------------------
    | Hashing Algorithm
    |--------------------------------------------------------------------------
    |
    | This option controls the hashing algorithm used for generating event hashes.
    | Supported algorithms are those supported by PHP's hash() function.
    |
    */
    'hashing_algorithm' => env('TRACEABILITY_HASH_ALGORITHM', 'sha256'),

    /*
    |--------------------------------------------------------------------------
    | Enable Merkle Proofs
    |--------------------------------------------------------------------------
    |
    | This option enables the generation of Merkle proofs for trace events,
    | allowing for efficient verification of event inclusion in the chain
    | without revealing the entire chain.
    |
    */
    'enable_merkle_proofs' => env('TRACEABILITY_ENABLE_MERKLE_PROOFS', true),

    /*
    |--------------------------------------------------------------------------
    | Enable Event Encryption
    |--------------------------------------------------------------------------
    |
    | This option enables encryption of sensitive event data before storage.
    | The encrypted data can only be decrypted with the application key.
    |
    */
    'enable_encryption' => env('TRACEABILITY_ENABLE_ENCRYPTION', true),

    /*
    |--------------------------------------------------------------------------
    | Enable Digital Signatures
    |--------------------------------------------------------------------------
    |
    | This option enables digital signatures for trace events, ensuring that
    | events cannot be tampered with after creation.
    |
    */
    'enable_digital_signatures' => env('TRACEABILITY_ENABLE_SIGNATURES', false),

    /*
    |--------------------------------------------------------------------------
    | Signature Private Key
    |--------------------------------------------------------------------------
    |
    | The private key used for signing trace events. This should be a
    | cryptographically secure private key stored in your .env file.
    |
    */
    'signature_private_key' => env('TRACEABILITY_SIGNATURE_PRIVATE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Signature Public Key
    |--------------------------------------------------------------------------
    |
    | The public key used for verifying trace event signatures. This should be
    | the public key corresponding to the private key in your .env file.
    |
    */
    'signature_public_key' => env('TRACEABILITY_SIGNATURE_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Event Expiration
    |--------------------------------------------------------------------------
    |
    | The default number of days before trace events are considered expired.
    | Set to null to disable event expiration.
    |
    */
    'default_event_expiration_days' => env('TRACEABILITY_DEFAULT_EVENT_EXPIRATION_DAYS', 365 * 5), // 5 years

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | This option controls whether audit logging is enabled for trace events.
    | When enabled, all changes to trace events will be logged to the
    | audit log table.
    |
    */
    'enable_audit_logging' => env('TRACEABILITY_ENABLE_AUDIT_LOGGING', true),

    /*
    |--------------------------------------------------------------------------
    | Allowed IP Addresses
    |--------------------------------------------------------------------------
    |
    | An array of IP addresses that are allowed to create or modify trace events.
    | Leave empty to allow all IP addresses.
    |
    */
    'allowed_ip_addresses' => array_filter(explode(',', env('TRACEABILITY_ALLOWED_IPS', ''))),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | These options control the rate limiting for trace event creation and
    | modification. This helps prevent abuse of the traceability system.
    |
    */
    'rate_limiting' => [
        'enabled' => env('TRACEABILITY_RATE_LIMITING_ENABLED', true),
        'max_attempts' => env('TRACEABILITY_RATE_LIMIT_ATTEMPTS', 60),
        'decay_minutes' => env('TRACEABILITY_RATE_LIMIT_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Blockchain Integration
    |--------------------------------------------------------------------------
    |
    | These options control the integration with external blockchain networks
    | for additional immutability guarantees.
    |
    */
    'blockchain' => [
        'enabled' => env('TRACEABILITY_BLOCKCHAIN_ENABLED', false),
        'network' => env('TRACEABILITY_BLOCKCHAIN_NETWORK', 'ethereum'),
        'contract_address' => env('TRACEABILITY_BLOCKCHAIN_CONTRACT_ADDRESS'),
        'private_key' => env('TRACEABILITY_BLOCKCHAIN_PRIVATE_KEY'),
        'gas_limit' => env('TRACEABILITY_BLOCKCHAIN_GAS_LIMIT', 300000),
        'gas_price' => env('TRACEABILITY_BLOCKCHAIN_GAS_PRICE', 20), // in gwei
    ],
];
