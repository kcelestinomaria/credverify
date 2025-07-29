<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\BlockchainService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockchainServiceTest extends TestCase
{
    use RefreshDatabase;

    private $blockchainService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->blockchainService = app(BlockchainService::class);
    }

    public function test_merkle_tree_generation()
    {
        $credentialHashes = [
            hash('sha256', 'credential1'),
            hash('sha256', 'credential2'),
            hash('sha256', 'credential3'),
            hash('sha256', 'credential4'),
        ];

        $merkleTree = $this->blockchainService->generateMerkleTree($credentialHashes);

        $this->assertArrayHasKey('root', $merkleTree);
        $this->assertArrayHasKey('tree', $merkleTree);
        $this->assertArrayHasKey('leaves', $merkleTree);
        $this->assertEquals($credentialHashes, $merkleTree['leaves']);
        $this->assertNotEmpty($merkleTree['root']);
    }

    public function test_merkle_proof_generation_and_verification()
    {
        $credentialHashes = [
            hash('sha256', 'credential1'),
            hash('sha256', 'credential2'),
            hash('sha256', 'credential3'),
            hash('sha256', 'credential4'),
        ];

        $merkleTree = $this->blockchainService->generateMerkleTree($credentialHashes);
        $firstHash = $credentialHashes[0];
        
        $merkleProof = $this->blockchainService->generateMerkleProof($firstHash, $merkleTree);
        
        $this->assertIsArray($merkleProof);
        $this->assertNotEmpty($merkleProof);

        // Verify the proof
        $isValid = $this->blockchainService->verifyMerkleProof($firstHash, $merkleProof, $merkleTree['root']);
        $this->assertTrue($isValid);
    }

    public function test_institution_key_generation()
    {
        $institutionId = 999;
        $keys = $this->blockchainService->generateInstitutionKeys($institutionId);

        $this->assertArrayHasKey('private_key', $keys);
        $this->assertArrayHasKey('public_key', $keys);
        $this->assertNotEmpty($keys['private_key']);
        $this->assertNotEmpty($keys['public_key']);
        $this->assertStringContainsString('BEGIN RSA PRIVATE KEY', $keys['private_key']);
        $this->assertStringContainsString('BEGIN PUBLIC KEY', $keys['public_key']);
    }

    public function test_digital_signature_and_verification()
    {
        $institutionId = 999;
        
        // Generate keys first
        $this->blockchainService->generateInstitutionKeys($institutionId);
        
        $testData = [
            'test' => 'credential data',
            'timestamp' => time(),
            'issuer' => 'Test Institution'
        ];

        $signature = $this->blockchainService->signCredential($testData, $institutionId);

        $this->assertArrayHasKey('signature', $signature);
        $this->assertArrayHasKey('algorithm', $signature);
        $this->assertArrayHasKey('data_hash', $signature);
        $this->assertEquals('RS256', $signature['algorithm']);
        $this->assertNotEmpty($signature['signature']);

        // Verify the signature
        $isValid = $this->blockchainService->verifyCredentialSignature($testData, $signature['signature'], $institutionId);
        $this->assertTrue($isValid);
    }

    public function test_merkle_proof_fails_with_wrong_hash()
    {
        $credentialHashes = [
            hash('sha256', 'credential1'),
            hash('sha256', 'credential2'),
        ];

        $merkleTree = $this->blockchainService->generateMerkleTree($credentialHashes);
        $wrongHash = hash('sha256', 'wrong_credential');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Credential hash not found in Merkle tree');
        
        $this->blockchainService->generateMerkleProof($wrongHash, $merkleTree);
    }

    public function test_signature_verification_fails_with_wrong_data()
    {
        $institutionId = 999;
        
        // Generate keys first
        $this->blockchainService->generateInstitutionKeys($institutionId);
        
        $originalData = ['test' => 'original data'];
        $modifiedData = ['test' => 'modified data'];

        $signature = $this->blockchainService->signCredential($originalData, $institutionId);
        
        // Verify with modified data should fail
        $isValid = $this->blockchainService->verifyCredentialSignature($modifiedData, $signature['signature'], $institutionId);
        $this->assertFalse($isValid);
    }
}
