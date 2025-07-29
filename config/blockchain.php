<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blockchain Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for blockchain integration including Ethereum network
    | settings, smart contract addresses, and cryptographic keys.
    |
    */

    'ethereum_rpc_url' => env('ETHEREUM_RPC_URL', 'https://sepolia.infura.io/v3/your-project-id'),
    
    'contract_address' => env('BLOCKCHAIN_CONTRACT_ADDRESS', '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6'),
    
    'wallet_address' => env('BLOCKCHAIN_WALLET_ADDRESS', '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6'),
    
    'private_key' => env('BLOCKCHAIN_PRIVATE_KEY', ''),
    
    'public_key' => env('BLOCKCHAIN_PUBLIC_KEY', ''),
    
    'network' => env('BLOCKCHAIN_NETWORK', 'sepolia'), // mainnet, sepolia, goerli
    
    'gas_limit' => env('BLOCKCHAIN_GAS_LIMIT', 300000),
    
    'gas_price' => env('BLOCKCHAIN_GAS_PRICE', '20000000000'), // 20 Gwei
    
    'batch_size' => env('BLOCKCHAIN_BATCH_SIZE', 100), // Number of credentials per batch
    
    'confirmation_blocks' => env('BLOCKCHAIN_CONFIRMATION_BLOCKS', 12),
    
    /*
    |--------------------------------------------------------------------------
    | Blockcerts Configuration
    |--------------------------------------------------------------------------
    |
    | Settings specific to Blockcerts standard compliance
    |
    */
    
    'blockcerts' => [
        'context' => [
            'https://www.w3.org/2018/credentials/v1',
            'https://www.blockcerts.org/schema/3.0/context.json'
        ],
        
        'type' => [
            'VerifiableCredential',
            'BlockcertsCredential'
        ],
        
        'proof_type' => 'MerkleProof2019',
        
        'signature_suite' => 'RsaSignature2018',
        
        'verification_method' => env('BLOCKCERTS_VERIFICATION_METHOD', 'https://credverify.com/keys/'),
        
        'issuer_profile' => env('BLOCKCERTS_ISSUER_PROFILE', 'https://credverify.com/issuer-profile.json'),
        
        'revocation_list' => env('BLOCKCERTS_REVOCATION_LIST', 'https://credverify.com/revocation-list.json'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Cryptographic and security-related configurations
    |
    */
    
    'security' => [
        'key_length' => 2048,
        'hash_algorithm' => 'sha256',
        'signature_algorithm' => 'RS256',
        'encryption_algorithm' => 'AES-256-CBC',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Settings for development and testing environments
    |
    */
    
    'development' => [
        'mock_blockchain' => env('MOCK_BLOCKCHAIN', false),
        'auto_confirm_transactions' => env('AUTO_CONFIRM_TRANSACTIONS', true),
        'skip_gas_estimation' => env('SKIP_GAS_ESTIMATION', true),
    ],
]; 