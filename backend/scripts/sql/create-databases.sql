-- ============================================================
-- ASTACODE ERP ORCHESTRATOR — Database Provisioning Script
-- Idempotent creation of isolated databases per microservice
-- Character Set: utf8mb4, Collation: utf8mb4_unicode_ci
-- ============================================================

-- 1. Core Service Database (Auth, RBAC, Audit, Files, Numbering, Settings)
CREATE DATABASE IF NOT EXISTS `astacode_core` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 2. CRM Service Database (Leads, Quotations, RAB, Contracts, WhatsApp Care)
CREATE DATABASE IF NOT EXISTS `astacode_crm` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 3. Marketing Service Database (Content Studio, Campaigns, Trends, Omnichannel)
CREATE DATABASE IF NOT EXISTS `astacode_marketing` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 4. HRD Service Database (Employees, Attendance, Payroll, ATS, Workload)
CREATE DATABASE IF NOT EXISTS `astacode_hrd` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 5. Project Management Service Database (Projects, Kanban, Sprints, QA, Tickets)
CREATE DATABASE IF NOT EXISTS `astacode_pm` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 6. Finance Service Database (COA, Journal, Invoicing, AP, P&L, Bank)
CREATE DATABASE IF NOT EXISTS `astacode_finance` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 7. AI Gateway Service Database (Prompt Registry, AI Runs, Vector Embeddings)
CREATE DATABASE IF NOT EXISTS `astacode_ai` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- 8. Orchestrator Service Database (Event Log, Sagas, Subscriptions, Rule Engine)
CREATE DATABASE IF NOT EXISTS `astacode_orchestrator` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- ============================================================
-- Optional: Dedicated Service DB Users with Least Privileges
-- (Used in production / staging environments)
-- ============================================================

-- CREATE USER IF NOT EXISTS 'astacode_core'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_core`.* TO 'astacode_core'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_crm'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_crm`.* TO 'astacode_crm'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_marketing'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_marketing`.* TO 'astacode_marketing'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_hrd'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_hrd`.* TO 'astacode_hrd'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_pm'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_pm`.* TO 'astacode_pm'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_finance'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_finance`.* TO 'astacode_finance'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_ai'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_ai`.* TO 'astacode_ai'@'%';

-- CREATE USER IF NOT EXISTS 'astacode_orchestrator'@'%' IDENTIFIED BY 'astacode_secret';
-- GRANT ALL PRIVILEGES ON `astacode_orchestrator`.* TO 'astacode_orchestrator'@'%';

-- FLUSH PRIVILEGES;

