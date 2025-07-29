# CredVerify: Blockchain-based Credential Verification

CredVerify is a secure, decentralized platform for issuing and verifying academic and professional credentials using blockchain technology.

## Key Features

- **Blockchain-Anchored Credentials**
- **Multi-Institution Support**
- **Instant Verification**
- **QR Code Verification**
- **Credential Revocation**
- **Verification Analytics**

## Tech Stack

- **Backend**: Laravel 10.x
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Blockchain**: Ethereum, Web3.php
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze

## Core Components

1. **Credential Management**
   - Issue digital credentials
   - Batch processing
   - W3C Verifiable Credentials format

2. **Blockchain Integration**
   - Smart contract anchoring
   - Merkle root hashing
   - Transaction management

3. **Verification System**
   - Public verification portal
   - API endpoints
   - QR code scanning

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Configure database and blockchain settings
5. Run migrations: `php artisan migrate`
6. Start the server: `php artisan serve`

## Configuration

Edit `.env` file:
```
DB_CONNECTION=mysql
DB_DATABASE=credverify
DB_USERNAME=root
DB_PASSWORD=

ETHEREUM_RPC_URL=https://sepolia.infura.io/v3/your-project-id
BLOCKCHAIN_CONTRACT_ADDRESS=0x...
```

## License

MIT License
