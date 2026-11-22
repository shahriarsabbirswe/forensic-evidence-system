# Digital Forensic Investigation & Evidence Management System (DFEMS)

A web-based platform designed for law enforcement agencies, investigation units, and digital forensic laboratories to manage digital evidence securely from the moment of seizure until courtroom presentation.

Digital evidence loses admissibility in court if the investigating agency cannot prove an unbroken chain of custody and guarantee that the data remained unaltered. Traditional paper registers and spreadsheets are prone to late entries, unauthorized edits, loss, and lack cryptographic integrity verification. DFEMS automates cryptographic verification and enforces strict chain-of-custody accountability across the entire investigation lifecycle.

Every evidence item is hashed using **SHA-256** upon intake and can be re-verified on demand or on a schedule. Each custody handover requires dual sign-off from both the releasing and receiving parties. All system actions are recorded in an append-only, hash-chained audit ledger that cannot be modified or deleted by any user, including system administrators.

**Live Deployment:** [https://dfems.tech](https://dfems.tech)

---

## Legal Compliance & Regulatory Basis

The system architecture and workflows are engineered to comply with the **Cyber Security Act 2026** (Act No. 81 of 2026, Bangladesh Gazette, 10 April 2026):

| Section | Statutory Provision | System Implementation |
| --- | --- | --- |
| **Section 43** | Admissibility of digital forensic evidence; mandatory preservation of evidence prepared in a Digital Forensic Lab | Core architectural foundation for append-only ledger and integrity verification |
| **Section 36** | Mandatory data preservation for 90 days, extendable by the Tribunal up to 180 days | Automated preservation expiry tracking and lifecycle retention policies per evidence item |
| **Section 32** | Investigation completion within 90 days, with extensions of 15 days (Controlling Officer) and 30 days (Tribunal) | Three-stage statutory case deadline tracking and extension management |
| **Sections 33(4), 35(2)** | Mandatory reporting of seized items and submission of search/forensic reports to the court | Automated court-ready forensic PDF report generation (FR11) |

---

## Technology Stack

| Layer | Technologies |
| --- | --- |
| **Languages** | PHP 8.3, JavaScript (ES6+), SQL |
| **Backend Framework** | Laravel 13 |
| **Frontend** | Blade Templates, Alpine.js, Chart.js |
| **Styling & Build** | Tailwind CSS, Vite |
| **Database** | MySQL 8 with Eloquent ORM |
| **Authentication & RBAC** | Laravel Breeze, Spatie Laravel Permission |
| **Document & Data Export** | DomPDF, Laravel Excel |
| **Deployment & Hosting** | Northflank Cloud (Containerized PHP/Nginx) |

---

## System Features & Functional Modules

### Module 1: Case Registration & Evidence Intake
| ID | Feature | Status |
| --- | --- | --- |
| **FR1** | Case creation, assignment, and statutory deadline management | Done |
| **FR2** | Digital and physical evidence registration with seizure metadata | Done |
| **FR3** | Automatic SHA-256 cryptographic hash calculation at intake | Done |
| **FR4** | Multi-criteria case, evidence, and SHA-256 hash search with filtering | Done |

### Module 2: Chain of Custody & Integrity Assurance
| ID | Feature | Status |
| --- | --- | --- |
| **FR5** | Immutable, append-only chain-of-custody ledger | Done |
| **FR6** | Custody transfer workflow with supervisor approval and dual sign-off | Done |
| **FR7** | Scheduled and on-demand cryptographic integrity verification | Done |
| **FR8** | Tamper-evident, hash-chained system audit log | Done |

### Module 3: Analysis, Reporting & Evidence Lifecycle
| ID | Feature | Status |
| --- | --- | --- |
| **FR9** | Forensic examination notes and findings management | Done |
| **FR10** | Chronological investigation timeline reconstruction | Done |
| **FR11** | Court-ready forensic report preview and PDF generation | Done |
| **FR12** | Evidence retention policy enforcement, archival, and approved disposal | Done |

*Supporting infrastructure includes role-based access control (Investigating Officer, Forensic Examiner, Evidence Custodian, Supervisor, Prosecutor, System Administrator), real-time activity notifications, and an operational analytics dashboard.*

---

## Non-Functional Requirements

* **Data Integrity & Immutability:** Evidence metadata, chain-of-custody entries, and audit logs are strictly append-only and cryptographically hash-chained. Any attempt to alter or delete historical records is blocked at the application and model layers and logged as a security event.
* **Security & Role-Based Access Control (RBAC):** All traffic is served over HTTPS, credentials are hashed with bcrypt, and permissions strictly enforce separation of duties—for example, Prosecutors can inspect evidence and download court reports but cannot modify case records or custody states.

---

## Local Development Setup

### Prerequisites
* PHP 8.3 or higher
* Composer 2
* Node.js 18 or higher & NPM
* MySQL 8.0+ (Laragon Full is recommended on Windows)

### Installation Steps

```bash
# 1. Clone the repository
git clone [https://github.com/shahriarsabbirswe/forensic-evidence-system.git](https://github.com/shahriarsabbirswe/forensic-evidence-system.git)
cd forensic-evidence-system

# 2. Install PHP and frontend dependencies
composer install
npm install

# 3. Configure environment variables
cp .env.example .env
php artisan key:generate
