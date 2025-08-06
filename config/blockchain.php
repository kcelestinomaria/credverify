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

    // Ethereum RPC URL (Infura, Alchemy, or your own node)
    'ethereum_rpc_url' => env('ETHEREUM_RPC_URL', 'https://sepolia.infura.io/v3/' . env('INFURA_PROJECT_ID')),
    
    // Smart contract addresses for different networks
    'contracts' => [
        'sepolia' => env('SEPOLIA_CONTRACT_ADDRESS', '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6'),
        'goerli' => env('GOERLI_CONTRACT_ADDRESS', '0x0000000000000000000000000000000000000000'),
        'mainnet' => env('MAINNET_CONTRACT_ADDRESS', '0x0000000000000000000000000000000000000000'),
    ],
    
    // Default contract address (will be set based on network)
    'contract_address' => function() {
        $network = config('blockchain.network', 'sepolia');
        return config("blockchain.contracts.{$network}");
    },
    
    // Wallet configuration
    'wallet_address' => env('BLOCKCHAIN_WALLET_ADDRESS', ''),
    'private_key' => env('BLOCKCHAIN_PRIVATE_KEY', ''),
    'public_key' => env('BLOCKCHAIN_PUBLIC_KEY', ''),
    
    // Network configuration
    'network' => env('BLOCKCHAIN_NETWORK', 'sepolia'), // mainnet, sepolia, goerli
    'chain_id' => [
        'mainnet' => 1,
        'sepolia' => 11155111,
        'goerli' => 5,
    ][env('BLOCKCHAIN_NETWORK', 'sepolia')],
    
    // Transaction settings
    'gas_limit' => env('BLOCKCHAIN_GAS_LIMIT', 300000),
    'gas_price' => env('BLOCKCHAIN_GAS_PRICE', '20000000000'), // 20 Gwei
    'max_priority_fee' => env('BLOCKCHAIN_MAX_PRIORITY_FEE', '2000000000'), // 2 Gwei for EIP-1559
    'max_fee' => env('BLOCKCHAIN_MAX_FEE', '50000000000'), // 50 Gwei for EIP-1559
    
    // Batch processing
    'batch_size' => env('BLOCKCHAIN_BATCH_SIZE', 10), // Reduced for testing
    'confirmation_blocks' => env('BLOCKCHAIN_CONFIRMATION_BLOCKS', 12),
    'max_retries' => env('BLOCKCHAIN_MAX_RETRIES', 3),
    'retry_delay' => env('BLOCKCHAIN_RETRY_DELAY', 15), // seconds
    
    // Explorer URLs for different networks
    'explorers' => [
        'mainnet' => 'https://etherscan.io',
        'sepolia' => 'https://sepolia.etherscan.io',
        'goerli' => 'https://goerli.etherscan.io',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Blockcerts Configuration
    |--------------------------------------------------------------------------
    |
    | Settings specific to Blockcerts standard compliance
    |
    */
    
    'blockcerts' => [
        // Contexts for Blockcerts v3.0
        'context' => [
            'https://www.w3.org/2018/credentials/v1',
            'https://www.blockcerts.org/schema/3.0/context.json',
            'https://w3id.org/security/suites/ed25519-2020/v1',
            'https://w3id.org/security/suites/x25519-2020/v1'
        ],
        
        // Default types for Blockcerts
        'type' => [
            'VerifiableCredential',
            'BlockcertsCredential',
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