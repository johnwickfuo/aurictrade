-- =============================================================================
-- Multicurrency support: schema patch
-- Adds fields to the `users` table for user-initiated currency-change requests
-- that require admin approval.
--
-- Safe to import on the production database. All new columns are nullable and
-- use defaults that match the existing application behaviour. Existing data is
-- not modified.
--
-- After import, run (from the project root):
--     php artisan config:clear && php artisan view:clear
-- =============================================================================

START TRANSACTION;

-- requested_currency: ISO-like code the user is requesting (e.g. 'EUR', 'BTC')
ALTER TABLE `users`
    ADD COLUMN `requested_currency` VARCHAR(10) NULL DEFAULT NULL
    AFTER `s_currency`;

-- requested_currency_symbol: HTML-encoded symbol matching the requested code
ALTER TABLE `users`
    ADD COLUMN `requested_currency_symbol` VARCHAR(50) NULL DEFAULT NULL
    AFTER `requested_currency`;

-- currency_change_status: 'pending' while awaiting admin action.
-- NULL once resolved (approved updates currency/s_currency and clears these
-- request fields; rejected just clears them).
ALTER TABLE `users`
    ADD COLUMN `currency_change_status` VARCHAR(20) NULL DEFAULT NULL
    AFTER `requested_currency_symbol`;

ALTER TABLE `users`
    ADD COLUMN `currency_change_requested_at` TIMESTAMP NULL DEFAULT NULL
    AFTER `currency_change_status`;

ALTER TABLE `users`
    ADD COLUMN `currency_change_resolved_at` TIMESTAMP NULL DEFAULT NULL
    AFTER `currency_change_requested_at`;

ALTER TABLE `users`
    ADD COLUMN `currency_change_admin_note` TEXT NULL DEFAULT NULL
    AFTER `currency_change_resolved_at`;

-- Index speeds up the admin "pending requests" listing.
ALTER TABLE `users`
    ADD INDEX `users_currency_change_status_index` (`currency_change_status`);

COMMIT;
