-- =========================================================
-- STOCKPILOT 360
-- Smart Inventory & Supply Chain Management System
-- Database: MySQL / MariaDB
-- Environment: XAMPP
-- Initial core schema
-- =========================================================

CREATE DATABASE IF NOT EXISTS stockpilot360
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE stockpilot360;

-- WARNING:
-- The DROP statements below delete existing data in these tables.
-- Use only for a fresh development database.
-- No default user account or password is created.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS inventory_balances;
DROP TABLE IF EXISTS product_suppliers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS warehouses;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS units;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS companies;

SET FOREIGN_KEY_CHECKS = 1;


-- =========================================================
-- 1. COMPANIES
-- =========================================================

CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(40) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_companies_slug (slug)
) ENGINE=InnoDB;


-- =========================================================
-- 2. ROLES
-- =========================================================

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB;


-- =========================================================
-- 3. PERMISSIONS
-- =========================================================

CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(120) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_permissions_key (permission_key)
) ENGINE=InnoDB;


-- =========================================================
-- 4. USERS
-- =========================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(160) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'locked')
        NOT NULL DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    UNIQUE KEY uq_users_company_email (company_id, email),
    KEY idx_users_status (status)
) ENGINE=InnoDB;


-- =========================================================
-- 5. USER ROLES
-- =========================================================

CREATE TABLE user_roles (
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, role_id),

    CONSTRAINT fk_user_roles_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user_roles_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================
-- 6. ROLE PERMISSIONS
-- =========================================================

CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (role_id, permission_id),

    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id)
        REFERENCES permissions(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================
-- 7. AUDIT LOGS
-- =========================================================

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    entity_type VARCHAR(120) NULL,
    entity_id BIGINT UNSIGNED NULL,
    details JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_logs_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    CONSTRAINT fk_audit_logs_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    KEY idx_audit_company_created (company_id, created_at),
    KEY idx_audit_entity (entity_type, entity_id)
) ENGINE=InnoDB;


-- =========================================================
-- 8. CATEGORIES
-- =========================================================

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_categories_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    UNIQUE KEY uq_categories_company_name (company_id, name)
) ENGINE=InnoDB;


-- =========================================================
-- 9. UNITS
-- =========================================================

CREATE TABLE units (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    abbreviation VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_units_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    UNIQUE KEY uq_units_company_name (company_id, name),
    UNIQUE KEY uq_units_company_abbreviation
        (company_id, abbreviation)
) ENGINE=InnoDB;


-- =========================================================
-- 10. SUPPLIERS
-- =========================================================

CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(40) NULL,
    address TEXT NULL,
    lead_time_days SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_suppliers_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    UNIQUE KEY uq_suppliers_company_name (company_id, name),
    KEY idx_suppliers_company_status (company_id, status)
) ENGINE=InnoDB;


-- =========================================================
-- 11. WAREHOUSES
-- =========================================================

CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(160) NOT NULL,
    code VARCHAR(40) NOT NULL,
    address TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_warehouses_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    UNIQUE KEY uq_warehouses_company_code (company_id, code),
    KEY idx_warehouses_company_status (company_id, status)
) ENGINE=InnoDB;


-- =========================================================
-- 12. PRODUCTS
-- =========================================================

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    unit_id BIGINT UNSIGNED NULL,
    sku VARCHAR(80) NOT NULL,
    barcode VARCHAR(100) NULL,
    name VARCHAR(180) NOT NULL,
    description TEXT NULL,
    cost_price DECIMAL(13,2) NOT NULL DEFAULT 0.00,
    selling_price DECIMAL(13,2) NOT NULL DEFAULT 0.00,
    reorder_level DECIMAL(13,3) NOT NULL DEFAULT 0.000,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_products_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_products_unit
        FOREIGN KEY (unit_id)
        REFERENCES units(id)
        ON DELETE SET NULL,

    UNIQUE KEY uq_products_company_sku (company_id, sku),
    UNIQUE KEY uq_products_company_barcode (company_id, barcode),
    KEY idx_products_company_name (company_id, name),
    KEY idx_products_status (status)
) ENGINE=InnoDB;


-- =========================================================
-- 13. PRODUCT SUPPLIERS
-- =========================================================

CREATE TABLE product_suppliers (
    product_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    supplier_sku VARCHAR(100) NULL,
    last_cost DECIMAL(13,2) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (product_id, supplier_id),

    CONSTRAINT fk_product_suppliers_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_product_suppliers_supplier
        FOREIGN KEY (supplier_id)
        REFERENCES suppliers(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================
-- 14. INVENTORY BALANCES
-- =========================================================

CREATE TABLE inventory_balances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity_on_hand DECIMAL(15,3) NOT NULL DEFAULT 0.000,
    quantity_reserved DECIMAL(15,3) NOT NULL DEFAULT 0.000,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_balances_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    CONSTRAINT fk_balances_warehouse
        FOREIGN KEY (warehouse_id)
        REFERENCES warehouses(id),

    CONSTRAINT fk_balances_product
        FOREIGN KEY (product_id)
        REFERENCES products(id),

    UNIQUE KEY uq_balance_warehouse_product
        (warehouse_id, product_id),

    KEY idx_balances_company_product (company_id, product_id)
) ENGINE=InnoDB;


-- =========================================================
-- 15. STOCK MOVEMENTS
-- =========================================================

CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    movement_type ENUM(
        'opening',
        'receipt',
        'sale',
        'transfer_in',
        'transfer_out',
        'adjustment',
        'return'
    ) NOT NULL,
    quantity_delta DECIMAL(15,3) NOT NULL,
    reference_type VARCHAR(80) NULL,
    reference_id BIGINT UNSIGNED NULL,
    reason VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_movements_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id),

    CONSTRAINT fk_movements_warehouse
        FOREIGN KEY (warehouse_id)
        REFERENCES warehouses(id),

    CONSTRAINT fk_movements_product
        FOREIGN KEY (product_id)
        REFERENCES products(id),

    CONSTRAINT fk_movements_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE SET NULL,

    KEY idx_movements_company_created (company_id, created_at),
    KEY idx_movements_product_warehouse
        (product_id, warehouse_id, created_at),
    KEY idx_movements_reference (reference_type, reference_id)
) ENGINE=InnoDB;


-- =========================================================
-- STARTER DATA: ROLES
-- =========================================================

INSERT IGNORE INTO roles (name, description) VALUES
('Super Admin', 'Company-wide administration'),
('Inventory Manager', 'Products and inventory operations'),
('Purchase Manager', 'Suppliers and purchasing'),
('Warehouse Staff', 'Receiving, dispatch, and stock counts'),
('Business Owner/Auditor', 'Read-only reporting and audit access');


-- =========================================================
-- STARTER DATA: PERMISSIONS
-- =========================================================

INSERT IGNORE INTO permissions (permission_key, description) VALUES
('dashboard.view', 'View dashboard'),
('products.view', 'View products'),
('products.manage', 'Create and update products'),
('suppliers.manage', 'Manage suppliers'),
('warehouses.manage', 'Manage warehouses'),
('inventory.view', 'View inventory'),
('inventory.adjust', 'Request or approve stock adjustments'),
('purchases.manage', 'Manage purchase orders'),
('transfers.manage', 'Manage warehouse transfers');

-- End of StockPilot 360 starter schema