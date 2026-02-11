# Digital Asset Custody Platform

A full-stack web application for managing precious metal custody accounts with support for both allocated (bar-level tracking) and unallocated (pooled) storage models.

## 🎯 Overview

This system provides a minimal but complete prototype for a digital asset custody platform that handles:
- Customer account management (Retail & Institutional)
- Deposits with dual storage models
- Withdrawals with balance validation
- Real-time asset valuation
- Multi-metal support (Gold, Silver, Platinum extensible)

## 🏗️ Architecture

### Technology Stack
- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Livewire 4 + Tailwind CSS 4
- **Database**: MySQL (SQL-based)
- **Real-time**: Livewire for reactive UI without JavaScript complexity

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         Browser (UI)                        │
│                  Livewire Components + Tailwind             │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP/WebSocket
┌────────────────────────▼────────────────────────────────────┐
│                    Laravel Application                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │  Livewire    │  │   Service    │  │   Models     │       │
│  │  Components  │──│    Layer     │──│  (Eloquent)  │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
│                           │                                 │
│                    ┌──────▼──────┐                          │
│                    │  Validation │                          │
│                    │  & Business │                          │
│                    │    Logic    │                          │
│                    └──────┬──────┘                          │
└───────────────────────────┼─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                      MySQL Database                         │
│  accounts | deposits | withdrawals | allocated_bars         │
│  metals | metal_prices                                      │
└─────────────────────────────────────────────────────────────┘
```

### Data Model

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│  accounts   │         │   metals    │         │metal_prices │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id          │         │ id          │         │ id          │
│ account_no  │         │ name        │         │ metal_id    │
│ customer    │         │ symbol      │         │ price_per_kg│
│ type        │         └──────┬──────┘         │ created_at  │
└──────┬──────┘                │                └─────────────┘
       │                       │
       │    ┌──────────────────┴──────────────────┐
       │    │                                      │
       ├────▼──────────┐                  ┌───────▼────────┐
       │   deposits    │                  │  withdrawals   │
       ├───────────────┤                  ├────────────────┤
       │ id            │                  │ id             │
       │ account_id    │                  │ account_id     │
       │ metal_id      │                  │ metal_id       │
       │ storage_type  │                  │ quantity_kg    │
       │ quantity_kg   │                  │ withdrawal_no  │
       │ deposit_no    │                  └────────────────┘
       └───────┬───────┘
               │
       ┌───────▼───────────┐
       │  allocated_bars   │
       ├───────────────────┤
       │ id                │
       │ serial_number     │
       │ account_id        │
       │ metal_id          │
       │ deposit_id        │
       └───────────────────┘
```

### Service Layer Architecture

**DepositService**: Handles deposit creation with transaction safety
- Validates storage type requirements
- Creates allocated bars for institutional accounts
- Prevents duplicate serial numbers

**WithdrawalService**: Manages withdrawals with balance checks
- Validates sufficient balance before withdrawal
- Handles bar deletion for allocated withdrawals
- Prevents overdrafts

**BalanceService**: Calculates holdings and valuations
- Computes unallocated balances (deposits - withdrawals)
- Tracks allocated bars per account
- Calculates real-time valuations using latest metal prices

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 5.7+ or MariaDB 10.3+

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd digital-asset-custody-platform
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=custody_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. **Run migrations and seed data**
```bash
php artisan migrate --seed
```

This will create:
- Database tables
- Sample metals (Gold)
- Sample accounts (5 retail, 5 institutional)
- Initial metal prices

7. **Build frontend assets**
```bash
npm run build
```

8. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.


## 📖 Usage Guide

### Creating an Account
1. Navigate to the dashboard
2. Click the "+" button
3. Enter customer name and select account type:
   - **Retail**: For individual customers (unallocated storage only)
   - **Institutional**: For corporate clients (allocated storage with bar tracking)

### Making a Deposit
1. Click the deposit icon (↓) next to an account
2. Select metal type (Gold)
3. Enter quantity in kilograms
4. For institutional accounts, provide serial numbers (one per kg, line-separated)

### Making a Withdrawal
1. Click the withdrawal icon (↑) next to an account
2. Select metal type
3. **Retail accounts**: Enter quantity to withdraw
4. **Institutional accounts**: Select specific bars to withdraw

### Viewing Account Details
Click the eye icon to view:
- Complete transaction history
- Current balances by metal
- Allocated bars (for institutional accounts)
- Total portfolio valuation

## 🎨 Key Features

### Dual Storage Models
- **Unallocated (Pooled)**: Assets pooled together, tracked by weight only
- **Allocated (Segregated)**: Individual bars with unique serial numbers

### Account Type Enforcement
- Retail accounts → Unallocated storage only
- Institutional accounts → Allocated storage only
- System prevents mixing storage types per account/metal combination

### Real-time Validation
- Balance checks before withdrawals
- Duplicate serial number prevention
- Storage type consistency enforcement

### Modern UI/UX
- Responsive design with Tailwind CSS
- Real-time updates via Livewire
- Intuitive modal-based workflows
- Color-coded account types and status indicators

## 🧪 Edge Cases & Design Decisions

### 1. **Insufficient Balance on Withdrawal**
**Scenario**: User attempts to withdraw more than available balance

**Handling**: 
- `WithdrawalService::createUnallocated()` checks balance before processing
- Throws exception: "Insufficient balance"
- Transaction rolls back, no partial withdrawal occurs

**Code**: `@app/Services/WithdrawalService.php:47-51`

### 2. **Duplicate Serial Numbers**
**Scenario**: Institutional deposit with serial number already in system

**Handling**:
- `DepositService::createDeposit()` validates uniqueness before insertion
- Throws exception: "Serial number already exists: {serial}"
- Database transaction ensures atomicity - no bars created if any duplicate found

**Code**: `@app/Services/DepositService.php:71-73`

### 3. **Account Type Mismatch**
**Scenario**: Retail account attempts allocated deposit (or vice versa)

**Handling**:
- Storage type defaults based on account type (retail=unallocated, institutional=allocated)
- Validation enforces: "Retail accounts can only have unallocated deposits"
- Business rule: One storage model per account type

**Code**: `@app/Livewire/Dashboard.php:141-147`

### 4. **Bar Selection Validation for Allocated Withdrawals**
**Scenario**: User selects bars not belonging to their account

**Handling**:
- `WithdrawalService::createAllocated()` validates each bar
- Checks: bar exists, belongs to account, matches metal type
- Throws exception: "Invalid bar selected"
- Prevents unauthorized bar withdrawals

**Code**: `@app/Services/WithdrawalService.php:76-80`

### 5. **Serial Number Count Mismatch**
**Scenario**: Depositing 5kg gold but providing 3 serial numbers

**Handling**:
- Frontend validation: counts serial numbers vs quantity
- Error: "Number of serial numbers (3) must match quantity in kg (5)"
- Assumption: 1 bar = 1kg for simplicity
- Prevents incomplete bar tracking

**Code**: `@app/Livewire/Dashboard.php:165-171`

## 🔧 Architecture Decisions

### 1. **Why Livewire over API + SPA?**
- Faster development for prototype
- Real-time reactivity without complex state management
- Reduced boilerplate (no API serialization, CORS, auth tokens)
- Suitable for internal tools and MVP validation

### 2. **Service Layer Pattern**
- Separates business logic from controllers/components
- Enables reusability (same service for API or CLI)
- Testable in isolation
- Transaction management centralized

### 3. **Account Type Determines Storage Type**
- Simplifies user experience (one less decision)
- Reflects real-world custody practices
- Retail customers typically don't need bar-level tracking
- Institutional clients require audit trails

### 4. **1 Bar = 1kg Assumption**
- Simplifies prototype implementation
- Real-world: bars vary (400oz gold bars ≈ 12.4kg)
- **Future**: Add `weight_kg` column to `allocated_bars` table

### 5. **No Authentication**
- Out of scope for prototype
- Production: Laravel Sanctum or Passport
- Assumes internal tool or trusted environment

### 6. **Single Currency (USD implied)**
- Metal prices stored as price_per_kg without currency field
- **Future**: Add `currency` column and conversion rates

### 7. **No Audit Trail**
- Deposits/withdrawals are immutable (no updates/deletes)
- Timestamps track when transactions occurred
- **Future**: Add `audit_logs` table for compliance

## 🧩 Extensibility

### Adding New Metals (Silver, Platinum)

1. **Add to database**:
```bash
php artisan tinker
```
```php
\App\Models\Metal::create(['name' => 'Silver', 'symbol' => 'XAG']);
\App\Models\Metal::create(['name' => 'Platinum', 'symbol' => 'XPT']);
```

2. **Add prices**:
```php
\App\Models\MetalPrice::create([
    'metal_id' => 2, // Silver
    'price_per_kg' => 25000,
]);
```

3. **No code changes required** - system is metal-agnostic

### Adding New Account Types

1. Update migration: `@database/migrations/2026_02_10_064048_create_accounts_table.php`
2. Add to enum: `'retail', 'institutional', 'corporate'`
3. Define storage rules in `Dashboard::saveDeposit()`

### API Layer (Future)

Service layer is API-ready:
```php
// Example API controller
public function deposit(Request $request) {
    $deposit = app(DepositService::class)->createDeposit(
        $request->account_id,
        $request->metal_id,
        $request->storage_type,
        $request->quantity_kg,
        $request->serial_numbers
    );
    return response()->json($deposit);
}
```

## 📊 Database Seeding

Default seed data includes:
- **Metals**: Gold (XAU)
- **Accounts**: 10 sample accounts (5 retail, 5 institutional)
- **Prices**: Initial gold price (~$65,000/kg)

Run seeders individually:
```bash
php artisan db:seed --class=MetalSeeder
php artisan db:seed --class=AccountSeeder
php artisan db:seed --class=MetalPriceSeeder
```

## 🧪 Testing

### Manual Testing Scenarios

1. **Happy Path - Retail Deposit & Withdrawal**
   - Create retail account
   - Deposit 10kg gold (unallocated)
   - Withdraw 3kg gold
   - Verify balance: 7kg

2. **Happy Path - Institutional Deposit & Withdrawal**
   - Create institutional account
   - Deposit 5kg gold with serials: BAR001-BAR005
   - Withdraw 2 bars
   - Verify 3 bars remain

3. **Edge Case - Overdraft Prevention**
   - Account with 5kg balance
   - Attempt to withdraw 10kg
   - Verify error message

4. **Edge Case - Duplicate Serial**
   - Deposit with serial BAR001
   - Attempt second deposit with BAR001
   - Verify rejection

### Automated Testing (Future)
```bash
php artisan test
```

Current: No test suite (prototype scope)
Recommended: PHPUnit tests for services, Feature tests for workflows

## 🔐 Security Considerations

### Current (Prototype)
- No authentication/authorization
- No rate limiting
- No input sanitization beyond validation
- No CSRF protection (Livewire handles this)

### Production Recommendations
1. Add Laravel Sanctum for API auth
2. Implement role-based access control (RBAC)
3. Add audit logging for compliance
4. Enable rate limiting on withdrawal endpoints
5. Add two-factor authentication for high-value operations
6. Encrypt sensitive data at rest
7. Implement IP whitelisting for admin functions

## 📝 Assumptions

1. **1 bar = 1kg**: Simplifies bar counting and serial number validation
2. **Single currency**: All prices in USD (implied)
3. **No fees**: Deposits/withdrawals are free
4. **Instant settlement**: No pending/clearing states
5. **Single vault**: No multi-location tracking
6. **No fractional bars**: Allocated storage uses whole bars only
7. **Trusted environment**: No authentication required for prototype
8. **Serial numbers are unique globally**: Not scoped to metal type or account
9. **Prices updated manually**: No external API integration
10. **No regulatory compliance**: KYC/AML out of scope

## 🚧 Known Limitations

1. **No account deletion**: Prevents orphaned transactions
2. **No transaction reversal**: Deposits/withdrawals are final
3. **No multi-user support**: Single-tenant design
4. **No reporting**: No PDF statements or export functionality
5. **No notifications**: No email/SMS alerts
6. **Basic error handling**: Production needs comprehensive logging
7. **No caching**: All queries hit database (acceptable for prototype)
8. **No pagination on account details**: Could be slow with many transactions

## 🎯 Future Enhancements

### Phase 2 Features
- [ ] Multi-currency support
- [ ] Real-time price feeds (API integration)
- [ ] PDF account statements
- [ ] Email notifications
- [ ] Advanced reporting dashboard
- [ ] Transaction search and filtering
- [ ] Bulk operations (CSV import)

### Phase 3 Features
- [ ] Mobile app (React Native)
- [ ] RESTful API with documentation
- [ ] Blockchain integration for audit trail
- [ ] Multi-vault support with transfer functionality
- [ ] Automated rebalancing
- [ ] Tax reporting (1099-B forms)

## 📚 Code Structure

```
app/
├── Livewire/          # UI Components
│   ├── Dashboard.php       # Main account listing
│   ├── AccountView.php     # Account detail view
│   ├── Deposits.php        # Deposit history
│   └── Withdrawals.php     # Withdrawal history
├── Models/            # Eloquent Models
│   ├── Account.php
│   ├── Deposit.php
│   ├── Withdrawal.php
│   ├── Metal.php
│   ├── MetalPrice.php
│   └── AllocatedBar.php
└── Services/          # Business Logic
    ├── DepositService.php
    ├── WithdrawalService.php
    └── BalanceService.php

database/
├── migrations/        # Database Schema
└── seeders/          # Sample Data

resources/
└── views/
    └── livewire/     # Blade Templates
```

## 🤝 Contributing

This is a prototype/assessment project. For production use:
1. Add comprehensive test coverage
2. Implement authentication
3. Add API documentation (OpenAPI/Swagger)
4. Set up CI/CD pipeline
5. Add monitoring and alerting

## 📄 License

MIT License - See LICENSE file for details

## 👤 Author

Developed as a technical assessment for a digital asset custody platform.

---

**Note**: This is a prototype implementation. Production deployment requires additional security hardening, testing, and compliance measures.
