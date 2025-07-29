<?php

namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;
use phpseclib\Crypt\RSA;
use phpseclib\Crypt\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlockchainService
{
    private $web3;
    private $contractAddress;
    private $contractAbi;
    private $privateKey;
    private $publicKey;

    public function __construct()
    {
        // Initialize Web3 connection (using Infura or local node)
        $this->web3 = new Web3(config('blockchain.ethereum_rpc_url', 'https://sepolia.infura.io/v3/your-project-id'));
        $this->contractAddress = config('blockchain.contract_address');
        $this->contractAbi = $this->getContractAbi();
        $this->initializeKeys();
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
    private function initializeKeys()
    {
        $this->privateKey = config('blockchain.private_key');
        $this->publicKey = config('blockchain.public_key');
    }

    /**
     * Send transaction to blockchain
     */
    private function sendTransaction($data)
    {
        // This is a simplified version - in production, you'd need proper
        // gas estimation, nonce management, and transaction signing
        
        $transactionParams = [
            'from' => config('blockchain.wallet_address'),
            'to' => $this->contractAddress,
            'data' => $data,
            'gas' => '0x76c0', // 30400
            'gasPrice' => '0x9184e72a000' // 10000000000000
        ];

        // In a real implementation, you'd sign this transaction
        // and send it via eth_sendRawTransaction
        
        // For now, return a mock transaction hash
        return '0x' . bin2hex(random_bytes(32));
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