
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMPTZ NULL,
    password VARCHAR(255) NOT NULL,
    two_factor_secret TEXT NULL,
    two_factor_recovery_codes TEXT NULL,
    two_factor_confirmed_at TIMESTAMPTZ NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NULL
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE CASCADE,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX idx_sessions_user_id ON sessions (user_id);
CREATE INDEX idx_sessions_last_activity ON sessions (last_activity);

CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);
CREATE INDEX idx_cache_expiration ON cache (expiration);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);
CREATE INDEX idx_cache_locks_expiration ON cache_locks (expiration);

CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);
CREATE INDEX idx_jobs_queue_reserved_available ON jobs (queue, reserved_at, available_at);

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMPTZ NULL,
    expires_at TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);
CREATE INDEX idx_pat_tokenable ON personal_access_tokens (tokenable_type, tokenable_id);
CREATE INDEX idx_pat_expires_at ON personal_access_tokens (expires_at);

CREATE TABLE payment_orders (
    id BIGSERIAL PRIMARY KEY,
    reff VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(150) NOT NULL,
    hp VARCHAR(20) NOT NULL CHECK (hp ~ '^[0-9]+$'),
    code VARCHAR(30) NOT NULL CHECK (code = '8834' || hp),

    base_amount BIGINT NOT NULL CHECK (base_amount > 0),
    fee BIGINT NOT NULL DEFAULT 2500 CHECK (fee = 2500),
    amount BIGINT NOT NULL CHECK (amount = base_amount + fee),

    expired_at TIMESTAMPTZ NOT NULL,
    paid_at TIMESTAMPTZ NULL,

    status VARCHAR(20) NOT NULL DEFAULT 'pending'
        CHECK (status IN ('pending', 'paid', 'expired')),

    flagged_by_user_id BIGINT NULL,
    flagged_at TIMESTAMPTZ NULL,

    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_payment_orders_flagged_by
        FOREIGN KEY (flagged_by_user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT chk_payment_orders_paid_rule
        CHECK (
            (status = 'paid' AND paid_at IS NOT NULL)
            OR
            (status IN ('pending', 'expired') AND paid_at IS NULL)
        )
);

CREATE INDEX idx_payment_orders_status_created
    ON payment_orders (status, created_at DESC);

CREATE INDEX idx_payment_orders_expired_at
    ON payment_orders (expired_at);

CREATE INDEX idx_payment_orders_paid_at
    ON payment_orders (paid_at);

CREATE INDEX idx_payment_orders_hp
    ON payment_orders (hp);



CREATE TABLE payment_transactions (
    id BIGSERIAL PRIMARY KEY,
    payment_order_id BIGINT NOT NULL,
    reff VARCHAR(50) NOT NULL,

    status VARCHAR(20) NOT NULL
        CHECK (status IN ('paid', 'expired')),

    source VARCHAR(20) NOT NULL DEFAULT 'api'
        CHECK (source IN ('api', 'dashboard', 'system')),

    acted_by_user_id BIGINT NULL,

    transacted_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    amount BIGINT NOT NULL CHECK (amount > 0),
    customer_name VARCHAR(150) NOT NULL,
    code VARCHAR(30) NOT NULL,
    expired_at TIMESTAMPTZ NOT NULL,

    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_payment_transactions_order
        FOREIGN KEY (payment_order_id)
        REFERENCES payment_orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_payment_transactions_actor
        FOREIGN KEY (acted_by_user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);


CREATE UNIQUE INDEX uq_payment_transactions_paid_once
    ON payment_transactions (payment_order_id)
    WHERE status = 'paid';

CREATE INDEX idx_payment_transactions_order_id
    ON payment_transactions (payment_order_id);

CREATE INDEX idx_payment_transactions_reff
    ON payment_transactions (reff);

CREATE INDEX idx_payment_transactions_status
    ON payment_transactions (status);

CREATE INDEX idx_payment_transactions_transacted_at
    ON payment_transactions (transacted_at DESC);



CREATE TABLE payment_transaction_backups (
    id BIGSERIAL PRIMARY KEY,
    payment_transaction_id BIGINT NOT NULL UNIQUE,
    payment_order_id BIGINT NOT NULL,

    reff VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL
        CHECK (status IN ('paid', 'expired')),

    source VARCHAR(20) NOT NULL
        CHECK (source IN ('api', 'dashboard', 'system')),

    acted_by_user_id BIGINT NULL,

    transacted_at TIMESTAMPTZ NOT NULL,
    amount BIGINT NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    code VARCHAR(30) NOT NULL,
    expired_at TIMESTAMPTZ NOT NULL,

    backed_up_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_payment_transaction_backups_tx
        FOREIGN KEY (payment_transaction_id)
        REFERENCES payment_transactions(id),

    CONSTRAINT fk_payment_transaction_backups_order
        FOREIGN KEY (payment_order_id)
        REFERENCES payment_orders(id),

    CONSTRAINT fk_payment_transaction_backups_actor
        FOREIGN KEY (acted_by_user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE INDEX idx_payment_transaction_backups_reff
    ON payment_transaction_backups (reff);

CREATE INDEX idx_payment_transaction_backups_order_id
    ON payment_transaction_backups (payment_order_id);

CREATE INDEX idx_payment_transaction_backups_backed_up_at
    ON payment_transaction_backups (backed_up_at DESC);