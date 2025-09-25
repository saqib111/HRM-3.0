EZGLOBAL HRM (HRM 3.0)

A production-grade Human Resource Management system built with Laravel for multi-company, multi-office operations.
It unifies biometric attendance, leave management, payroll deductions, permissions, and large-scale imports/exports in one platform.

Deployed across multiple offices (e.g., Sihanoukville, Bavet, Poipet, Malaysia, TWFM) for 1,700+ employees. Designed for reliability, auditability, and performance.

✨ Highlights

Biometric Attendance (ZKTeco)

Device connect/disable/enable, full log ingestion, idempotent upserts.

Automated 3:00 AM device restarts with a grace window to prevent false alerts.

Advanced Leave Engine

Yearly buckets: Birthday, Sick, Hospitalization.

Lifetime buckets: Maternity, Paternity, Marriage, Miscarriage.

Annual Leave with auto-split to Unpaid when balance is insufficient.

Half-day & multi-range requests in a single submission.

Multi-stage approvals: Leader → Manager → HR with timestamps.

Payroll Deductions (Policy-Driven)

Absentee fines: 4% (6-day week) or 4.8% (5-day week) per missing day.

Lateness fines per 15 minutes: 0.125% (6-day) or 0.25% (5-day).

Avoids double-counting; respects scheduled day-off categories (PH, BT, etc.).

Granular Access Control

Roles: Superadmin, HR, Payroll Admin, Leader, Employee.

Fine-grained, per-user permissions (e.g., show_al_balance, update_al_balance).

Company/office scoping in queries and UI filters.

Built for Scale

Server-side DataTables, queued Excel imports/exports (≈500k rows).

Optimized SQL paths, indices, and idempotent pipelines.

🧩 Core Modules
Attendance & Biometrics

ZKTeco SDK (TCP 4370) integration.

Safe ingestion into check_verifies with unique key (device_ip, user_id, fingerprint_in).

Full device dump utility for one-off data recovery.

Scheduler + logs for operational visibility.

Leave Management

Tables: annual_leaves, yearly_leaves, lifetime_leaves, leave_management.

Constraints: Birthday once/year; Marriage lifetime cap; Off-days excluded from balances.

Approval chain with approver IDs and timestamps.

Payroll & Deductions

Policy engine implemented in SQL/Laravel pipelines.

Exports via Laravel-Excel with queues.

Users, Companies & Permissions

Multi-company/groups (e.g., EZGB A–E, Malaysia, TWFM).

Role + permission middleware; IP whitelist support; device-fingerprint alerts (optional).

🏗️ Architecture

Backend: Laravel (PHP 8.x), MySQL/MariaDB, Redis (queues/cache)

Frontend: Blade + DataTables (some screens have React/Inertia variants in related projects)

Background: Laravel Scheduler, Supervisor-managed workers

ZKTeco Devices → Ingestion → check_verifies → Attendance/Leave/Payroll
                                 │
                                 └── 03:00 restart + 5-min grace window
Web UI → Roles/Permissions → Approvals → Payroll → Exports

📚 Key Tables

users, user_profiles, companies, departments, designations

check_verifies (raw device logs; unique index recommended)

attendance_record (normalized day records)

annual_leaves, yearly_leaves, lifetime_leaves

leave_management (requests; ranges; half-days; off-days; approvals)

assigned_leave_approvals, user_permissions

whitelist_ips, manage_ip_restrictions
