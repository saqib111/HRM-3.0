<!-- Header -->
<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel">
</p>

<h1 align="center">HUMAN RESOURCE MANAGEMENT SYSTEM (HRM 3.0)</h1>
<p align="center">
  A production-grade Human Resource Management system built with <strong>Laravel</strong> for multi-company, multi-office environments.
</p>

<p align="center">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/License-Proprietary-lightgrey">
  <img alt="Build" src="https://img.shields.io/badge/Status-Production-success">
</p>

<hr>

<!-- Summary -->
<h2 id="about">About the Project</h2>
<p>
  <strong>HRM</strong> centralizes biometric attendance, leave workflows, payroll deductions, permissions, and 
  large-scale data import/export into a single, reliable platform. It is actively used in production by <strong>1,700+ users</strong> across multiple offices (no location data disclosed for privacy).
</p>

<ul>
  <li><strong>Biometric Attendance (ZKTeco):</strong> device integration, safe ingestion, duplicate-proof upserts.</li>
  <li><strong>Advanced Leave Management:</strong> annual, yearly, and lifetime buckets (birthday, sick, hospitalization, maternity, paternity, marriage, miscarriage), half-days, multi-range requests.</li>
  <li><strong>Multi-Stage Approvals:</strong> leader → manager → HR with audit timestamps.</li>
  <li><strong>Payroll Deductions:</strong> policy-driven rules for absences and lateness (no double counting, respects official day-offs).</li>
  <li><strong>Granular Access Control:</strong> roles plus per-user permissions; company/office scoping in queries and UI.</li>
  <li><strong>Scale Ready:</strong> queued Excel imports (~500k rows), server-side DataTables, optimized SQL and indexes.</li>
</ul>

<hr>

<!-- Features -->
<h2 id="features">Key Features</h2>

<h3>1) Attendance &amp; Biometrics</h3>
<ul>
  <li>ZKTeco SDK integration (TCP 4370): connect/disable/enable/fetch logs.</li>
  <li>03:00 device restart job with a 5-minute grace window to avoid false alarms.</li>
  <li>Idempotent ingestion into <code>check_verifies</code> with a unique key on <code>(device_ip, user_id, fingerprint_in)</code>.</li>
  <li>Single-device full-dump utility for data recovery or backfilling.</li>
</ul>

<h3>2) Leave Management</h3>
<ul>
  <li>Tables: <code>annual_leaves</code>, <code>yearly_leaves</code>, <code>lifetime_leaves</code>, <code>leave_management</code>.</li>
  <li>Constraints: birthday once per year; lifetime caps (e.g., marriage leave); off-days excluded from balances.</li>
  <li>Auto-split annual leave to unpaid when balance is insufficient (with confirmation).</li>
  <li>Half-day support and multiple date ranges in a single request.</li>
  <li>Approvals: leader → manager → HR, with approver IDs and timestamps.</li>
</ul>

<h3>3) Payroll &amp; Deductions</h3>
<ul>
  <li>Absentee: percentage-based per missing working day (supports 5-day/6-day week rules).</li>
  <li>Lateness: configurable fines per 15 minutes (policy-driven).</li>
  <li>No double counting; honors scheduled day-off categories.</li>
  <li>Excel exports via queued writers.</li>
</ul>

<h3>4) Users, Companies &amp; Permissions</h3>
<ul>
  <li>Multi-company structure with office scoping in UI and SQL.</li>
  <li>Roles: Superadmin, HR, Payroll Admin, Leader, Employee.</li>
  <li>Fine-grained per-user permissions (e.g., <code>show_al_balance</code>, <code>update_al_balance</code>).</li>
  <li>Optional login hardening: IP whitelist and device-fingerprint alerts.</li>
</ul>

<hr>

<!-- Architecture -->
<h2 id="architecture">Architecture</h2>
<ul>
  <li><strong>Backend:</strong> Laravel (PHP 8.x), MySQL/MariaDB, Redis (queues/cache)</li>
  <li><strong>Frontend:</strong> Blade + DataTables (with some React/Inertia variants in related projects)</li>
  <li><strong>Background:</strong> Laravel Scheduler + Supervisor-managed workers</li>
</ul>

<pre>
Devices (ZKTeco)  →  Ingestion  →  check_verifies  →  Attendance/Leave/Payroll
                         │
                         └─ 03:00 device restart + 5-minute grace window

Web UI  →  Roles/Permissions  →  Approvals  →  Payroll  →  Exports
</pre>

<hr>

<!-- Data Model -->
<h2 id="data-model">Key Tables</h2>
<ul>
  <li><code>users</code>, <code>user_profiles</code>, <code>companies</code>, <code>departments</code>, <code>designations</code></li>
  <li><code>check_verifies</code> (raw device logs; unique index recommended)</li>
  <li><code>attendance_record</code> (normalized per-day records)</li>
  <li><code>annual_leaves</code>, <code>yearly_leaves</code>, <code>lifetime_leaves</code></li>
  <li><code>leave_management</code> (requests; ranges; half-days; off-days; approvals)</li>
  <li><code>assigned_leave_approvals</code>, <code>user_permissions</code></li>
  <li><code>whitelist_ips</code>, <code>manage_ip_restrictions</code></li>
</ul>

<hr>

<!-- Quick Start -->
<h2 id="quick-start">Quick Start (Local)</h2>

<h4>Prerequisites</h4>
<ul>
  <li>PHP ≥ 8.2, Composer</li>
  <li>MySQL/MariaDB</li>
  <li>Node.js &amp; npm</li>
  <li>Redis (recommended for queues/cache)</li>
</ul>

<h4>Install &amp; Run</h4>
<pre><code>git clone &lt;repo-url&gt;
cd hrm
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install &amp;&amp; npm run build   # or: npm run dev
php artisan serve
</code></pre>

<h4>Queues &amp; Scheduler</h4>
<pre><code>php artisan queue:work
# cron: * * * * * php /path/to/artisan schedule:run
</code></pre>

<h4>Supervisor (example)</h4>
<pre><code>[program:hrm-queue]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --timeout=120
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/supervisor/hrm-queue.log
</code></pre>

<hr>

<!-- Environment -->
<h2 id="env">Environment (example)</h2>
<pre><code>APP_NAME="HRM"
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=file

# Biometric (placeholder values)
BIOMETRIC_PORT=4370
DEVICE_IPS=10.0.0.10,10.0.0.11
</code></pre>

<hr>

<!-- Evaluation -->
<h2 id="evaluation">How to Evaluate (5-Minute Tour)</h2>
<ol>
  <li><strong>Login</strong> as Admin → explore Users/Companies/Permissions.</li>
  <li><strong>Attendance</strong> → load biometric logs (seed or device dump) → verify server-side paging/search.</li>
  <li><strong>Leave</strong> → create a request with multiple ranges + half-day → approve Leader → Manager → HR.</li>
  <li><strong>Payroll</strong> → run deduction preview for a date range → export to Excel.</li>
  <li><strong>Security</strong> → test IP whitelist and permission-scoped views.</li>
</ol>

<hr>

<!-- Screens -->
<h2 id="screens">Screenshots</h2>
<p>
  Add images under <code>docs/screens/</code> and reference them here:
</p>
<ul>
  <li>Dashboard</li>
  <li>Biometric Attendance Logs</li>
  <li>Leave Request (multi-range)</li>
  <li>Approvals Timeline</li>
  <li>Payroll Deduction Report</li>
  <li>Permissions Matrix</li>
</ul>

<hr>

<!-- Credits -->
<h2 id="author">Author / Role</h2>
<p>
  <strong>Muhammad Saqib Sajjad</strong> — Full-stack developer &amp; system owner.<br>
  Responsibilities: biometric device integration, ingestion pipelines, leave engine (annual/yearly/lifetime), 
  multi-stage approvals, payroll deduction logic, performance tuning (queues, indexes, server-side DataTables), 
  and DevOps (Supervisor, scheduler, environment hardening).
</p>

<hr>

<!-- Privacy + License -->
<h2 id="privacy">Privacy &amp; License</h2>
<p>
  To protect organizational privacy, this repository omits sensitive deployment details, exact locations, and real device data. 
  The codebase and documentation represent the architecture and capabilities of the deployed system.
</p>
<p><strong>License:</strong> Proprietary — for demonstration and academic evaluation.</p>
