<?php

namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils as Web3Utils;
use phpseclib\Crypt\RSA;
use phpseclib\Crypt\EC;
use phpseclib\Crypt\Hash as CryptHash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use kornrunner\Keccak;
use InvalidArgumentException;

class BlockchainService
{
    private $web3;
    private $provider;
    private $contractAddress;
    private $contractAbi;
    private $privateKey;
    private $publicKey;
    private $network;
    private $chainId;
    private $walletAddress;
    private $gasLimit;
    private $gasPrice;
    private $maxPriorityFee;
    private $maxFee;
    private $contract;

    public function __construct()
    {
        $this->network = config('blockchain.network', 'sepolia');
        $this->chainId = config('blockchain.chain_id');
        $this->contractAddress = config('blockchain.contract_address');
        $this->walletAddress = config('blockchain.wallet_address');
        $this->gasLimit = config('blockchain.gas_limit');
        $this->gasPrice = config('blockchain.gas_price');
        $this->maxPriorityFee = config('blockchain.max_priority_fee');
        $this->maxFee = config('blockchain.max_fee');
        
        // Initialize Web3 provider with timeout
        $timeout = 30; // seconds
        $this->provider = new HttpProvider(new HttpRequestManager(config('blockchain.ethereum_rpc_url'), $timeout));
        $this->web3 = new Web3($this->provider);
        
        // Initialize contract ABI
        $this->contractAbi = $this->getContractAbi();
        
        // Initialize contract instance
        $this->contract = new Contract($this->provider, $this->contractAbi);
        
        // Initialize keys
        $this->initializeKeys();
        
        Log::info('BlockchainService initialized', [
            'network' => $this->network,
            'contract_address' => $this->contractAddress,
            'wallet_address' => $this->walletAddress,
            'chain_id' => $this->chainId
        ]);
    }

    /**
     * Generate RSA key pair for institution
     */
    public function generateInstitutionKeys($institutionId)
    {
        $rsa = new RSA();
        $keys = $rsa->createKey(2048);

        // Store keys securely
        Storage::disk('local')->put("keys/institution_{$institutionId}_private.pem", $keys['privatekey']);
        Storage::disk('local')->put("keys/institution_{$institutionId}_public.pem", $keys['publickey']);

        return [
            'private_key' => $keys['privatekey'],
            'public_key' => $keys['publickey']
        ];
    }

    /**
     * Sign credential data with institution's private key
     */
    public function signCredential($credentialData, $institutionId)
    {
        $privateKeyPath = storage_path("app/keys/institution_{$institutionId}_private.pem");
        
        if (!file_exists($privateKeyPath)) {
            throw new \Exception("Institution private key not found. Please generate keys first.");
        }

        $rsa = new RSA();
        $rsa->loadKey(file_get_contents($privateKeyPath));
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $rsa->setHash('sha256');

        $dataToSign = json_encode($credentialData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = $rsa->sign($dataToSign);

        return [
            'signature' => base64_encode($signature),
            'algorithm' => 'RS256',
            'data_hash' => hash('sha256', $dataToSign)
        ];
    }

    /**
     * Verify credential signature
     */
    public function verifyCredentialSignature($credentialData, $signature, $institutionId)
    {
        $publicKeyPath = storage_path("app/keys/institution_{$institutionId}_public.pem");
        
        if (!file_exists($publicKeyPath)) {
            return false;
        }

        $rsa = new RSA();
        $rsa->loadKey(file_get_contents($publicKeyPath));
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $rsa->setHash('sha256');

        $dataToVerify = json_encode($credentialData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signatureBytes = base64_decode($signature);

        return $rsa->verify($dataToVerify, $signatureBytes);
    }

    /**
     * Generate Merkle tree for batch of credentials
     */
    public function generateMerkleTree($credentialHashes)
    {
        if (empty($credentialHashes)) {
            throw new \Exception("Cannot generate Merkle tree from empty array");
        }

        // Ensure even number of hashes
        if (count($credentialHashes) % 2 !== 0) {
            $credentialHashes[] = end($credentialHashes); // Duplicate last hash
        }

        $tree = [$credentialHashes];
        $currentLevel = $credentialHashes;

        while (count($currentLevel) > 1) {
            $nextLevel = [];
            
            for ($i = 0; $i < count($currentLevel); $i += 2) {
                $left = $currentLevel[$i];
                $right = $currentLevel[$i + 1] ?? $currentLevel[$i];
                $combined = $left . $right;
                $nextLevel[] = hash('sha256', $combined);
            }
            
            $tree[] = $nextLevel;
            $currentLevel = $nextLevel;
        }

        return [
            'root' => $currentLevel[0],
            'tree' => $tree,
            'leaves' => $credentialHashes
        ];
    }

    /**
     * Generate Merkle proof for a specific credential
     */
    public function generateMerkleProof($credentialHash, $merkleTree)
    {
        $proof = [];
        $tree = $merkleTree['tree'];
        $index = array_search($credentialHash, $tree[0]);

        if ($index === false) {
            throw new \Exception("Credential hash not found in Merkle tree");
        }

        for ($level = 0; $level < count($tree) - 1; $level++) {
            $currentLevel = $tree[$level];
            $isRightNode = $index % 2 === 1;
            $siblingIndex = $isRightNode ? $index - 1 : $index + 1;

            if (isset($currentLevel[$siblingIndex])) {
                $proof[] = [
                    'hash' => $currentLevel[$siblingIndex],
                    'position' => $isRightNode ? 'left' : 'right'
                ];
            }

            $index = intval($index / 2);
        }

        return $proof;
    }

    /**
     * Verify Merkle proof
     */
    public function verifyMerkleProof($credentialHash, $merkleProof, $merkleRoot)
    {
        $computedHash = $credentialHash;

        foreach ($merkleProof as $proof) {
            if ($proof['position'] === 'left') {
                $computedHash = hash('sha256', $proof['hash'] . $computedHash);
            } else {
                $computedHash = hash('sha256', $computedHash . $proof['hash']);
            }
        }

        return $computedHash === $merkleRoot;
    }

    /**
     * Anchor Merkle root to Ethereum blockchain
     */
    public function anchorToBlockchain($merkleRoot, $batchId)
    {
        try {
            $contract = new Contract($this->web3->provider, $this->contractAbi);
            $contract->at($this->contractAddress);

            // Prepare transaction data
            $transactionData = $contract->getData('anchorCredentials', $merkleRoot, $batchId);

            // Send transaction (this would need proper gas estimation and signing)
            $transactionHash = $this->sendTransaction($transactionData);

            return [
                'transaction_hash' => $transactionHash,
                'merkle_root' => $merkleRoot,
                'batch_id' => $batchId,
                'timestamp' => now(),
                'blockchain' => 'ethereum'
            ];

        } catch (\Exception $e) {
            Log::error('Blockchain anchoring failed: ' . $e->getMessage());
            throw new \Exception('Failed to anchor to blockchain: ' . $e->getMessage());
        }
    }

    /**
     * Verify credential on blockchain
     */
    public function verifyOnBlockchain($merkleRoot, $batchId)
    {
        try {
            $contract = new Contract($this->web3->provider, $this->contractAbi);
            $contract->at($this->contractAddress);

            // Call contract method to verify
            $result = $contract->call('verifyCredentials', $merkleRoot, $batchId);

            return [
                'verified' => $result[0] ?? false,
                'timestamp' => $result[1] ?? null,
                'block_number' => $result[2] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Blockchain verification failed: ' . $e->getMessage());
            return ['verified' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create revocation registry entry
     */
    public function addToRevocationRegistry($credentialId, $reason = 'revoked')
    {
        try {
            $contract = new Contract($this->web3->provider, $this->contractAbi);
            $contract->at($this->contractAddress);

            $transactionData = $contract->getData('revokeCredential', $credentialId, $reason);
            $transactionHash = $this->sendTransaction($transactionData);

            return [
                'transaction_hash' => $transactionHash,
                'credential_id' => $credentialId,
                'reason' => $reason,
                'timestamp' => now()
            ];

        } catch (\Exception $e) {
            Log::error('Revocation registry update failed: ' . $e->getMessage());
            throw new \Exception('Failed to update revocation registry: ' . $e->getMessage());
        }
    }

    /**
     * Check if credential is revoked on blockchain
     */
    public function isRevokedOnBlockchain($credentialId)
    {
        try {
            $contract = new Contract($this->web3->provider, $this->contractAbi);
            $contract->at($this->contractAddress);

            $result = $contract->call('isRevoked', $credentialId);
            
            return [
                'is_revoked' => $result[0] ?? false,
                'revocation_date' => $result[1] ?? null,
                'reason' => $result[2] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Blockchain revocation check failed: ' . $e->getMessage());
            return ['is_revoked' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Initialize cryptographic keys
     */
    /**
     * Initialize cryptographic keys for blockchain operations
     */
    private function initializeKeys()
    {
        $this->privateKey = config('blockchain.private_key');
        $this->publicKey = config('blockchain.public_key');
        
        if (empty($this->privateKey) || empty($this->publicKey)) {
            Log::warning('Blockchain keys not configured. Some features may not work.');
        }
    }
    
    /**
     * Generate a new Ethereum-compatible key pair
     * 
     * @return array [privateKey, publicKey, address]
     */
    public function generateKeyPair()
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1'
        ];
        
        $res = openssl_pkey_new($config);
        
        // Get private key
        openssl_pkey_export($res, $privateKeyPem);
        
        // Get public key
        $publicKeyPem = openssl_pkey_get_details($res)['key'];
        
        // Extract public key in hex format
        $publicKeyHex = '0x' . bin2hex(openssl_pkey_get_details(openssl_pkey_get_public($publicKeyPem))['key']);
        
        // Derive Ethereum address from public key
        $publicKey = str_replace('0x', '', $publicKeyHex);
        $hash = Keccak::hash(hex2bin($publicKey), 256);
        $address = '0x' . substr($hash, -40);
        
        return [
            'private_key' => $privateKeyPem,
            'public_key' => $publicKeyPem,
            'address' => strtolower($address)
        ];
    }
    
    /**
     * Sign a message with the configured private key
     * 
     * @param string $message Message to sign
     * @return string Signature in hex format
     */
    public function signMessage($message)
    {
        if (empty($this->privateKey)) {
            throw new \RuntimeException('Private key not configured');
        }
        
        $messageHash = Keccak::hash(\kornrunner\Keccak::hash($message, 256), 256);
        $signature = '';
        $success = openssl_sign($messageHash, $signature, $this->privateKey, 'sha256');
        
        if (!$success) {
            throw new \RuntimeException('Failed to sign message');
        }
        
        return '0x' . bin2hex($signature);
    }
    
    /**
     * Verify a message signature
     * 
     * @param string $message Original message
     * @param string $signature Signature in hex format
     * @param string $publicKey Public key to verify with
     * @return bool True if signature is valid
     */
    public function verifySignature($message, $signature, $publicKey)
    {
        $messageHash = Keccak::hash(\kornrunner\Keccak::hash($message, 256), 256);
        $signature = str_replace('0x', '', $signature);
        
        if (strlen($signature) !== 130) {
            throw new \InvalidArgumentException('Invalid signature format');
        }
        
        $r = substr($signature, 0, 64);
        $s = substr($signature, 64, 64);
        $v = hexdec(substr($signature, 128, 2)) - 27;
        
        if ($v != ($v & 1)) {
            throw new \RuntimeException('Invalid signature: v is not 27 or 28');
        }
        
        $publicKey = $this->recoverPublicKey($messageHash, $r, $s, $v);
        $recoveredAddress = $this->publicKeyToAddress($publicKey);
        
        return strtolower($recoveredAddress) === strtolower($this->walletAddress);
    }
    
    /**
     * Recover public key from signature
     */
    private function recoverPublicKey($messageHash, $r, $s, $v)
    {
        $recId = $v - 27;
        
        // Create a compact signature
        $signature = '0x' . 
            str_pad($r, 64, '0', STR_PAD_LEFT) .
            str_pad($s, 64, '0', STR_PAD_LEFT) .
            dechex($recId + 27 + 4);
        
        // Recover the public key
        $publicKey = '';
        $this->web3->personal->ecRecover($messageHash, $signature, function ($err, $result) use (&$publicKey) {
            if ($err !== null) {
                throw new \RuntimeException('Failed to recover public key: ' . $err->getMessage());
            }
            $publicKey = $result;
        });
        
        return $publicKey;
    }
    
    /**
     * Convert public key to Ethereum address
     */
    private function publicKeyToAddress($publicKey)
    {
        $publicKey = str_replace('0x', '', $publicKey);
        
        if (strlen($publicKey) !== 130) {
            throw new \InvalidArgumentException('Invalid public key length');
        }
        
        // Remove '0x04' prefix and hash the public key
        $publicKey = substr($publicKey, 2);
        $hash = Keccak::hash(hex2bin($publicKey), 256);
        
        // Take last 20 bytes (40 chars) and prefix with '0x'
        return '0x' . substr($hash, -40);
    }

    /**
     * Send a signed transaction to the blockchain
     * 
     * @param array $transaction Transaction parameters
     * @return string Transaction hash
     */
    public function sendTransaction($transaction)
    {
        try {
            // Set default transaction parameters
            $defaults = [
                'from' => $this->walletAddress,
                'to' => $this->contractAddress,
                'gas' => Web3Utils::toHex($this->gasLimit, '0x0'),
                'gasPrice' => Web3Utils::toHex($this->gasPrice, '0x0'),
                'chainId' => Web3Utils::toHex($this->chainId, '0x0'),
                'nonce' => $this->getTransactionCount($this->walletAddress, 'pending'),
            ];
            
            // Merge with provided transaction data
            $transaction = array_merge($defaults, $transaction);
            
            // Sign the transaction
            $signedTx = $this->signTransaction($transaction);
            
            // Send the raw transaction
            $txHash = '';
            $this->web3->eth->sendRawTransaction($signedTx, function ($err, $result) use (&$txHash) {
                if ($err !== null) {
                    throw new \RuntimeException('Failed to send transaction: ' . $err->getMessage());
                }
                $txHash = $result;
            });
            
            Log::info('Transaction sent', [
                'tx_hash' => $txHash,
                'from' => $transaction['from'],
                'to' => $transaction['to'] ?? 'contract_creation',
                'value' => $transaction['value'] ?? '0',
                'gas' => $transaction['gas'],
                'gasPrice' => $transaction['gasPrice']
            ]);
            
            return $txHash;
            
        } catch (\Exception $e) {
            Log::error('Error sending transaction', [
                'error' => $e->getMessage(),
                'transaction' => $transaction ?? []
            ]);
            throw $e;
        }
    }
    
    /**
     * Sign a transaction with the configured private key
     */
    private function signTransaction($transaction)
    {
        if (empty($this->privateKey)) {
            throw new \RuntimeException('Private key not configured for transaction signing');
        }
        
        // Serialize the transaction
        $serializedTx = $this->serializeTransaction($transaction);
        
        // Hash the serialized transaction
        $txHash = Keccak::hash(hex2bin($serializedTx), 256);
        
        // Sign the hash
        $signature = '';
        $success = openssl_sign(hex2bin($txHash), $signature, $this->privateKey, 'sha256');
        
        if (!$success) {
            throw new \RuntimeException('Failed to sign transaction');
        }
        
        // Get the recovery ID (v)
        $r = bin2hex(substr($signature, 0, 32));
        $s = bin2hex(substr($signature, 32, 32));
        
        // Calculate v (recovery id)
        $v = dechex(27 + $this->calculateV($txHash, $r, $s, $this->publicKey));
        
        // Create the signature
        $signature = '0x' . $r . $s . $v;
        
        // Encode the transaction with signature
        return $this->encodeTransaction($transaction, $v, $r, $s);
    }
    
    /**
     * Calculate the V value for the signature
     */
    private function calculateV($hash, $r, $s, $publicKey)
    {
        // This is a simplified version - in a real implementation, you'd need to
        // recover the public key from the signature and compare it to the expected key
        return 27; // For mainnet, 28 for testnets
    }
    
    /**
     * Get the transaction count for an address
     */
    private function getTransactionCount($address, $block = 'latest')
    {
        $count = 0;
        $this->web3->eth->getTransactionCount($address, $block, function ($err, $result) use (&$count) {
            if ($err !== null) {
                throw new \RuntimeException('Failed to get transaction count: ' . $err->getMessage());
            }
            $count = $result->toString();
        });
        
        return $count;
    }
    
    /**
     * Serialize a transaction for signing
     */
    private function serializeTransaction($transaction)
    {
        // This is a simplified version - in a real implementation, you'd need to
        // properly RLP encode the transaction data
        $data = [
            'nonce' => $transaction['nonce'],
            'gasPrice' => $transaction['gasPrice'],
            'gas' => $transaction['gas'],
            'to' => $transaction['to'] ?? '',
            'value' => $transaction['value'] ?? '0x0',
            'data' => $transaction['data'] ?? '0x',
            'v' => Web3Utils::toHex($this->chainId, '0x0'),
            'r' => '0x',
            's' => '0x'
        ];
        
        return json_encode($data);
    }
    
    /**
     * Encode a signed transaction
     */
    private function encodeTransaction($tx, $v, $r, $s)
    {
        // This is a simplified version - in a real implementation, you'd need to
        // properly RLP encode the transaction data with the signature
        $data = [
            'nonce' => $tx['nonce'],
            'gasPrice' => $tx['gasPrice'],
            'gas' => $tx['gas'],
            'to' => $tx['to'] ?? '',
            'value' => $tx['value'] ?? '0x0',
            'data' => $tx['data'] ?? '0x',
            'v' => $v,
            'r' => $r,
            's' => $s
        ];
        
        return '0x' . bin2hex(json_encode($data));
    }
    
    /**
     * Generate a Merkle tree from an array of hashes
     * 
     * @param array $hashes Array of hex-encoded hashes
     * @return array [root, tree, leaves]
     */
    public function generateMerkleTree($hashes)
    {
        if (empty($hashes)) {
            throw new \InvalidArgumentException('Cannot generate Merkle tree from empty array');
        }
        
        // Ensure all hashes are properly formatted
        $leaves = array_map(function ($hash) {
            $hash = strtolower($hash);
            if (strpos($hash, '0x') === 0) {
                $hash = substr($hash, 2);
            }
            if (!preg_match('/^[0-9a-f]{64}$/', $hash)) {
                throw new \InvalidArgumentException('Invalid hash format: ' . $hash);
            }
            return $hash;
        }, $hashes);
        
        // Build the tree
        $tree = [$leaves];
        $level = $leaves;
        
        while (count($level) > 1) {
            $nextLevel = [];
            
            // Process pairs of nodes
            for ($i = 0; $i < count($level); $i += 2) {
                $left = $level[$i];
                $right = ($i + 1 < count($level)) ? $level[$i + 1] : $level[$i];
                
                // Concatenate and hash the pair
                $combined = $left . $right;
                $hash = Keccak::hash(hex2bin($combined), 256);
                $nextLevel[] = $hash;
            }
            
            $tree[] = $nextLevel;
            $level = $nextLevel;
        }
        
        $root = $level[0] ?? '';
        
        Log::debug('Merkle tree generated', [
            'leaves_count' => count($leaves),
            'root' => $root,
            'tree_depth' => count($tree)
        ]);
        
        return [
            'root' => $root,
            'tree' => $tree,
            'leaves' => $leaves
        ];
    }
    
    /**
     * Generate a Merkle proof for a specific hash
     * 
     * @param string $hash The hash to generate proof for
     * @param array $merkleTree The complete Merkle tree
     * @return array Merkle proof
     */
    public function generateMerkleProof($hash, $merkleTree)
    {
        $hash = strtolower($hash);
        if (strpos($hash, '0x') === 0) {
            $hash = substr($hash, 2);
        }
        
        $leaves = $merkleTree['leaves'] ?? [];
        $tree = $merkleTree['tree'] ?? [];
        
        // Find the index of the hash in the leaves
        $index = array_search($hash, $leaves);
        if ($index === false) {
            throw new \InvalidArgumentException('Hash not found in Merkle tree leaves');
        }
        
        $proof = [];
        $currentIndex = $index;
        
        // Build the proof by walking up the tree
        for ($level = 0; $level < count($tree) - 1; $level++) {
            $levelNodes = $tree[$level];
            $isRightNode = $currentIndex % 2 === 1;
            $siblingIndex = $isRightNode ? $currentIndex - 1 : $currentIndex + 1;
            
            if ($siblingIndex < count($levelNodes)) {
                $proof[] = [
                    'position' => $isRightNode ? 'left' : 'right',
                    'hash' => $levelNodes[$siblingIndex]
                ];
            }
            
            $currentIndex = (int)($currentIndex / 2);
        }
        
        return [
            'hash' => $hash,
            'index' => $index,
            'proof' => $proof,
            'root' => $merkleTree['root'] ?? '',
            'leaves_count' => count($leaves)
        ];
    }
    
    /**
     * Verify a Merkle proof
     * 
     * @param string $hash The original hash
     * @param array $proof The Merkle proof
     * @param string $root The expected Merkle root
     * @return bool True if the proof is valid
     */
    public function verifyMerkleProof($hash, $proof, $root)
    {
        $hash = strtolower($hash);
        if (strpos($hash, '0x') === 0) {
            $hash = substr($hash, 2);
        }
        
        $computedHash = $hash;
        
        foreach ($proof['proof'] as $node) {
            $nodeHash = strtolower($node['hash']);
            
            if ($node['position'] === 'left') {
                $computedHash = Keccak::hash(hex2bin($nodeHash . $computedHash), 256);
            } else {
                $computedHash = Keccak::hash(hex2bin($computedHash . $nodeHash), 256);
            }
        }
        
        return $computedHash === strtolower($root);
    }
    
    /**
     * Anchor a Merkle root to the blockchain
     * 
     * @param string $merkleRoot The Merkle root to anchor
     * @param string $batchId A unique batch identifier
     * @return string Transaction hash
     */
    public function anchorMerkleRoot($merkleRoot, $batchId)
    {
        try {
            // Prepare the transaction data
            $functionSignature = 'anchorMerkleRoot(bytes32,string)';
            $functionSelector = substr(Keccak::hash($functionSignature, 256), 0, 8);
            
            // Encode the parameters
            $merkleRootPadded = str_pad(substr($merkleRoot, 2), 64, '0', STR_PAD_LEFT);
            $batchIdEncoded = $this->encodeString($batchId);
            
            // Construct the data payload
            $data = '0x' . $functionSelector . $merkleRootPadded . $batchIdEncoded;
            
            // Send the transaction
            $txHash = $this->sendTransaction([
                'to' => $this->contractAddress,
                'data' => $data
            ]);
            
            Log::info('Merkle root anchored to blockchain', [
                'merkle_root' => $merkleRoot,
                'batch_id' => $batchId,
                'tx_hash' => $txHash
            ]);
            
            return $txHash;
            
        } catch (\Exception $e) {
            Log::error('Failed to anchor Merkle root', [
                'merkle_root' => $merkleRoot,
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Encode a string for ABI encoding
     */
    private function encodeString($string)
    {
        $length = strlen($string);
        $lengthHex = str_pad(dechex($length), 64, '0', STR_PAD_LEFT);
        
        // Convert string to hex
        $hex = bin2hex($string);
        
        // Pad to multiple of 32 bytes (64 hex chars)
        $paddedLength = ceil(strlen($hex) / 64) * 64;
        $hexPadded = str_pad($hex, $paddedLength, '0');
        
        return $lengthHex . $hexPadded;
    }
    
    /**
     * Verify if a Merkle root exists on the blockchain
     * 
     * @param string $merkleRoot The Merkle root to verify
     * @param string $batchId The batch ID
     * @return array Verification result
     */
    public function verifyMerkleRoot($merkleRoot, $batchId)
    {
        try {
            $result = [
                'verified' => false,
                'block_number' => null,
                'timestamp' => null,
                'error' => null
            ];
            
            $this->contract->at($this->contractAddress);
            
            // Call the smart contract
            $this->contract->call('verifyMerkleRoot', $merkleRoot, $batchId, function ($err, $response) use (&$result) {
                if ($err !== null) {
                    throw new \RuntimeException('Smart contract call failed: ' . $err->getMessage());
                }
                
                $result = [
                    'verified' => $response[0] ?? false,
                    'block_number' => $response[1] ?? 0,
                    'timestamp' => $response[2] ?? 0,
                    'error' => null
                ];
            });
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Error verifying Merkle root', [
                'merkle_root' => $merkleRoot,
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Get smart contract ABI
     */
    private function getContractAbi()
    {
        return json_encode([
            [
                "inputs" => [
                    ["name" => "merkleRoot", "type" => "bytes32"],
                    ["name" => "batchId", "type" => "string"]
                ],
                "name" => "anchorCredentials",
                "outputs" => [],
                "stateMutability" => "nonpayable",
                "type" => "function"
            ],
            [
                "inputs" => [
                    ["name" => "merkleRoot", "type" => "bytes32"],
                    ["name" => "batchId", "type" => "string"]
                ],
                "name" => "verifyCredentials",
                "outputs" => [
                    ["name" => "", "type" => "bool"],
                    ["name" => "", "type" => "uint256"],
                    ["name" => "", "type" => "uint256"]
                ],
                "stateMutability" => "view",
                "type" => "function"
            ],
            [
                "inputs" => [
                    ["name" => "credentialId", "type" => "string"],
                    ["name" => "reason", "type" => "string"]
                ],
                "name" => "revokeCredential",
                "outputs" => [],
                "stateMutability" => "nonpayable",
                "type" => "function"
            ],
            [
                "inputs" => [
                    ["name" => "credentialId", "type" => "string"]
                ],
                "name" => "isRevoked",
                "outputs" => [
                    ["name" => "", "type" => "bool"],
                    ["name" => "", "type" => "uint256"],
                    ["name" => "", "type" => "string"]
                ],
                "stateMutability" => "view",
                "type" => "function"
            ]
        ]);
    }
} 