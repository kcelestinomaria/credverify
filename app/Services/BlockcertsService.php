<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\Institution;
use App\Models\BlockchainAnchor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BlockcertsService
{
    private $blockchainService;

    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }

    /**
     * Generate a fully compliant Blockcerts credential
     */
    public function generateBlockcertsCredential(Credential $credential)
    {
        try {
            // 1. Generate digital signature
            $signatureData = $this->signCredential($credential);
            
            // 2. Create Blockcerts JSON structure
            $blockcertsData = $this->createBlockcertsStructure($credential, $signatureData);
            
            // 3. Update credential with signature data
            $credential->update([
                'digital_signature' => $signatureData['signature'],
                'signature_algorithm' => $signatureData['algorithm'],
                'signed_at' => now(),
                'blockcerts_metadata' => $blockcertsData
            ]);

            // 4. Save Blockcerts JSON file
            $jsonPath = $this->saveBlockcertsJson($credential, $blockcertsData);
            $credential->update(['json_path' => $jsonPath]);

            return [
                'success' => true,
                'credential' => $credential,
                'blockcerts_data' => $blockcertsData,
                'json_path' => $jsonPath
            ];

        } catch (\Exception $e) {
            Log::error('Blockcerts generation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process credentials for blockchain anchoring
     */
    public function processCredentialsForAnchoring($credentials)
    {
        try {
            // Generate batch ID
            $batchId = 'BATCH_' . now()->format('YmdHis') . '_' . uniqid();
            
            // Collect credential hashes
            $credentialHashes = [];
            foreach ($credentials as $credential) {
                $credentialHashes[] = $credential->hash;
                $credential->update(['batch_id' => $batchId]);
            }

            // Generate Merkle tree
            $merkleTree = $this->blockchainService->generateMerkleTree($credentialHashes);
            
            // Generate Merkle proofs for each credential
            foreach ($credentials as $index => $credential) {
                $merkleProof = $this->blockchainService->generateMerkleProof(
                    $credential->hash, 
                    $merkleTree
                );
                
                $credential->update([
                    'merkle_proof' => $merkleProof
                ]);
            }

            // Create blockchain anchor record
            $anchor = BlockchainAnchor::create([
                'batch_id' => $batchId,
                'merkle_root' => $merkleTree['root'],
                'blockchain' => 'ethereum',
                'network' => config('blockchain.network', 'sepolia'),
                'status' => 'pending'
            ]);

            // Anchor to blockchain
            $anchorResult = $this->blockchainService->anchorToBlockchain(
                $merkleTree['root'], 
                $batchId
            );

            // Update anchor with transaction details
            $anchor->update([
                'transaction_hash' => $anchorResult['transaction_hash'],
                'transaction_data' => $anchorResult,
                'anchored_at' => now()
            ]);

            // Update credentials as anchored
            foreach ($credentials as $credential) {
                $credential->update([
                    'blockchain_anchored' => true,
                    'anchored_at' => now()
                ]);
            }

            return [
                'success' => true,
                'batch_id' => $batchId,
                'merkle_root' => $merkleTree['root'],
                'transaction_hash' => $anchorResult['transaction_hash'],
                'anchor' => $anchor
            ];

        } catch (\Exception $e) {
            Log::error('Blockchain anchoring failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify a Blockcerts credential
     */
    public function verifyBlockcertsCredential(Credential $credential)
    {
        $verificationResults = [
            'credential_hash' => false,
            'digital_signature' => false,
            'merkle_proof' => false,
            'blockchain_anchor' => false,
            'revocation_status' => false,
            'overall_valid' => false
        ];

        try {
            // 1. Verify credential hash
            $verificationResults['credential_hash'] = $this->verifyCredentialHash($credential);

            // 2. Verify digital signature
            if ($credential->isSigned()) {
                $verificationResults['digital_signature'] = $this->verifyDigitalSignature($credential);
            }

            // 3. Verify Merkle proof
            if ($credential->hasMerkleProof()) {
                $verificationResults['merkle_proof'] = $this->verifyMerkleProof($credential);
            }

            // 4. Verify blockchain anchor
            if ($credential->isBlockchainAnchored()) {
                $verificationResults['blockchain_anchor'] = $this->verifyBlockchainAnchor($credential);
            }

            // 5. Check revocation status
            $verificationResults['revocation_status'] = !$credential->isRevokedOnBlockchain();

            // Overall validity
            $verificationResults['overall_valid'] = 
                $verificationResults['credential_hash'] &&
                $verificationResults['digital_signature'] &&
                $verificationResults['merkle_proof'] &&
                $verificationResults['blockchain_anchor'] &&
                $verificationResults['revocation_status'];

            return $verificationResults;

        } catch (\Exception $e) {
            Log::error('Blockcerts verification failed: ' . $e->getMessage());
            return array_merge($verificationResults, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Sign credential with institution's private key
     */
    private function signCredential(Credential $credential)
    {
        $credentialData = [
            'id' => $credential->verification_code,
            'type' => config('blockchain.blockcerts.type'),
            'issuer' => $this->getIssuerProfile($credential->institution),
            'issuanceDate' => $credential->issued_on->toISOString(),
            'credentialSubject' => [
                'id' => 'did:example:' . $credential->verification_code,
                'name' => $credential->full_name,
                'degree' => [
                    'type' => $credential->credential_type,
                    'name' => $credential->credential_type
                ]
            ]
        ];

        return $this->blockchainService->signCredential($credentialData, $credential->institution_id);
    }

    /**
     * Create Blockcerts JSON structure
     */
    private function createBlockcertsStructure(Credential $credential, array $signatureData)
    {
        $institution = $credential->institution;
        
        return [
            '@context' => config('blockchain.blockcerts.context'),
            'type' => config('blockchain.blockcerts.type'),
            'id' => url('/credentials/' . $credential->verification_code),
            'issuer' => $this->getIssuerProfile($institution),
            'issuanceDate' => $credential->issued_on->toISOString(),
            'credentialSubject' => [
                'id' => 'did:example:' . $credential->verification_code,
                'name' => $credential->full_name,
                'degree' => [
                    'type' => $credential->credential_type,
                    'name' => $credential->credential_type
                ]
            ],
            'proof' => [
                'type' => config('blockchain.blockcerts.proof_type'),
                'created' => now()->toISOString(),
                'verificationMethod' => config('blockchain.blockcerts.verification_method') . $institution->id,
                'proofPurpose' => 'assertionMethod',
                'jws' => $signatureData['signature']
            ],
            'credentialStatus' => [
                'id' => config('blockchain.blockcerts.revocation_list'),
                'type' => 'RevocationList2020Status',
                'revocationListIndex' => $credential->id,
                'revocationListCredential' => config('blockchain.blockcerts.revocation_list')
            ],
            'metadata' => [
                'hash' => $credential->hash,
                'verification_code' => $credential->verification_code,
                'batch_id' => $credential->batch_id,
                'blockchain_anchored' => $credential->blockchain_anchored,
                'anchored_at' => $credential->anchored_at?->toISOString(),
                'signature_algorithm' => $signatureData['algorithm'],
                'data_hash' => $signatureData['data_hash']
            ]
        ];
    }

    /**
     * Get issuer profile for institution
     */
    private function getIssuerProfile(Institution $institution)
    {
        return [
            'id' => url('/institutions/' . $institution->slug),
            'type' => 'Profile',
            'name' => $institution->name,
            'url' => $institution->website,
            'email' => $institution->email,
            'publicKey' => [
                'id' => config('blockchain.blockcerts.verification_method') . $institution->id,
                'type' => 'RsaVerificationKey2018'
            ]
        ];
    }

    /**
     * Save Blockcerts JSON to storage
     */
    private function saveBlockcertsJson(Credential $credential, array $blockcertsData)
    {
        $filename = 'blockcerts_' . $credential->verification_code . '.json';
        $path = 'credentials/json/' . $filename;
        
        Storage::disk('public')->put($path, json_encode($blockcertsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        return $path;
    }

    /**
     * Verify credential hash
     */
    private function verifyCredentialHash(Credential $credential)
    {
        if (!$credential->credential_file_path || !Storage::disk('public')->exists($credential->credential_file_path)) {
            return false;
        }

        $fileContent = Storage::disk('public')->get($credential->credential_file_path);
        $computedHash = hash('sha256', $fileContent);
        
        return $computedHash === $credential->hash;
    }

    /**
     * Verify digital signature
     */
    private function verifyDigitalSignature(Credential $credential)
    {
        if (!$credential->blockcerts_metadata) {
            return false;
        }

        $credentialData = $credential->blockcerts_metadata;
        unset($credentialData['proof']); // Remove proof for verification

        return $this->blockchainService->verifyCredentialSignature(
            $credentialData,
            $credential->digital_signature,
            $credential->institution_id
        );
    }

    /**
     * Verify Merkle proof
     */
    private function verifyMerkleProof(Credential $credential)
    {
        if (!$credential->merkle_proof || !$credential->blockchainAnchor) {
            return false;
        }

        return $this->blockchainService->verifyMerkleProof(
            $credential->hash,
            $credential->merkle_proof,
            $credential->blockchainAnchor->merkle_root
        );
    }

    /**
     * Verify blockchain anchor
     */
    private function verifyBlockchainAnchor(Credential $credential)
    {
        $anchor = $credential->blockchainAnchor;
        if (!$anchor || !$anchor->isConfirmed()) {
            return false;
        }

        // Verify on blockchain
        $blockchainResult = $this->blockchainService->verifyOnBlockchain(
            $anchor->merkle_root,
            $anchor->batch_id
        );

        return $blockchainResult['verified'] ?? false;
    }
} 