# Digital Forensic Investigation & Evidence Management System

A web platform for investigation agencies and forensic laboratories that manages digital evidence from the moment of seizure until it is presented in court.

Digital evidence loses its value in court if the agency cannot prove who handled it and that nobody altered it. Most investigation units still record seizures, transfers and storage in paper registers and spreadsheets, where entries can be written late, edited or lost, and where no cryptographic proof of integrity exists. This system makes that proof automatic.

Every item of evidence is hashed with SHA-256 when it is registered, and the hash can be re-checked at any time. Each handover is recorded as a chain of custody entry requiring confirmation from both the releasing and the receiving party. All activity is written to an append only, hash chained audit log that no user can edit, including the administrator.

**SE 331 Software Engineering Design Capstone Project, Fall 2026**
Daffodil International University

---

## Legal basis

The system is built around the Cyber Security Act 2026 (Act No. 81 of 2026, Bangladesh Gazette, 10 April 2026).

| Section | Provision | Where it appears in the system |
| --- | --- | --- |
| 43 | Forensic evidence is admissible; preservation of evidence prepared in a Digital Forensic Lab is mandatory | The legal basis for the whole system |
| 36 | Data preservation for 90 days, extendable by the Tribunal to 180 | Preservation expiry on every evidence record |
| 32 | Investigation within 90 days, plus 15 with the controlling officer, plus 30 with the Tribunal | Case deadline with a three stage extension |
| 33(4), 35(2) | The officer must inform the court about seized items and file the search report | Court ready report, FR11 |

---

## Technology

| Layer | Technology |
| --- | --- |
| Language | PHP 8.3, JavaScript, SQL |
| Backend framework | Laravel 13 |
| Frontend | Blade templates, Alpine.js, Chart.js |
| Styling | Tailwind CSS, bundled with Vite |
| Database | MySQL 8 with Eloquent ORM |
| Authentication | Laravel Breeze |
| Authorisation | Spatie Laravel Permission |
| Reports | DomPDF, Laravel Excel |
| Local environment | Laragon |

---

## Team

| ID | Name | Requirements |
| --- | --- | --- |
| 232-35-541 | Md. Abu Shahriar Sabbir | FR1, FR2, FR6, FR11 |
| 232-35-136 | Jarif Hossain | FR3, FR7, FR8, FR9 |
| 232-35-329 | Sabbir Ahmed | FR4, FR5, FR10, FR12 |

Group 05, Lab Section B1.

---

## Requirements and progress

### Module 1: Case Registration & Evidence Intake

| # | Requirement | Owner | Status |
| --- | --- | --- | --- |
| FR1 | Case creation and management | Md. Abu Shahriar Sabbir | Done |
| FR2 | Evidence registration with seizure metadata | Md. Abu Shahriar Sabbir | Done |
| FR3 | Automatic SHA-256 hash at intake | Jarif Hossain | Done |
| FR4 | Case and evidence search with filtering | Sabbir Ahmed | Done |

### Module 2: Chain of Custody & Integrity Assurance

| # | Requirement | Owner | Status |
| --- | --- | --- | --- |
| FR5 | Chain of custody ledger | Sabbir Ahmed | Done |
| FR6 | Transfer request with approval and dual sign off | Md. Abu Shahriar Sabbir | Done |
| FR7 | Scheduled and on demand integrity verification | Jarif Hossain | Done |
| FR8 | Tamper evident hash chained audit log | Jarif Hossain | Done |

### Module 3: Analysis, Reporting & Evidence Lifecycle

| # | Requirement | Owner | Status |
| --- | --- | --- | --- |
| FR9 | Analysis notes and findings management | Jarif Hossain | Not started |
| FR10 | Investigation timeline reconstruction | Sabbir Ahmed | Not started |
| FR11 | Court ready forensic report generation | Md. Abu Shahriar Sabbir | Not started |
| FR12 | Evidence retention, archival and disposal | Sabbir Ahmed | Not started |

Authentication, role enforcement, notifications and the dashboard are supporting features built around these twelve, not numbered requirements of their own.

---

## Non-functional requirements

**Integrity.** Evidence records, custody entries and audit logs are append only and hash chained. No user can edit or delete an entry, including the system administrator, and any such attempt is itself logged. The legal admissibility of the whole system depends on this.

**Security and access control.** Data is encrypted at rest and transmitted over HTTPS, passwords are hashed with bcrypt, and role based access control limits every role to its permitted actions. A Prosecutor can read reports but can never change an evidence record.

---

## Running it locally

Requires PHP 8.3 or higher, Composer 2, Node 18 or higher and MySQL 8. Laragon Full provides all of them.

```bash
git clone [https://github.com/shahriarsabbirswe/forensic-evidence-system.git](https://github.com/shahriarsabbirswe/forensic-evidence-system.git)
cd forensic-evidence-system
git checkout module-1-evidence-intake
composer install
cp .env.example .env
php artisan key:generate
