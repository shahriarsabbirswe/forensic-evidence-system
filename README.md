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

Md. Abu Shahriar Sabbir 
Jarif Hossain
Sabbir Ahmed 


## Requirements and progress

### Module 1: Case Registration & Evidence Intake

| # | Requirement | Owner | Status |
| --- | --- | --- | --- |
| FR1 | Case creation and management | Md. Abu Shahriar Sabbir | Done |
| FR2 | Evidence registration with seizure metadata | Md. Abu Shahriar Sabbir | Done |
| FR3 | Automatic SHA-256 hash at intake | Jarif Hossain | In progress |
| FR4 | Case and evidence search with filtering | Sabbir Ahmed | In progress |

### Module 2: Chain of Custody & Integrity Assurance

| # | Requirement | Owner | Status |
| --- | --- | --- | --- |
| FR5 | Chain of custody ledger | Sabbir Ahmed | Not started |
| FR6 | Transfer request with approval and dual sign off | Md. Abu Shahriar Sabbir | Not started |
| FR7 | Scheduled and on demand integrity verification | Jarif Hossain | Not started |
| FR8 | Tamper evident hash chained audit log | Jarif Hossain | Not started |

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
git clone https://github.com/shahriarsabbirswe/forensic-evidence-system.git
cd forensic-evidence-system
git checkout module-1-evidence-intake
composer install
cp .env.example .env
php artisan key:generate
```

Set the database section of `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forensic_evidence
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```bash
mysql -u root -e "CREATE DATABASE forensic_evidence;"
php artisan migrate --seed
npm install
npm run dev
```

In a second terminal:

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`.

---

## Design decisions worth knowing

**Nothing can be deleted.** There is no delete route for a case or for evidence anywhere in the system. Cases are closed or archived through their status field. Evidence is disposed of through FR12, with approval, and the disposal is recorded permanently. Evidence that can vanish defeats the purpose of the system.

**The hash is written in the same transaction as the record.** If an evidence record saved but its hash did not, that item would have no baseline and could never be verified again. Both happen together or neither does.

**The edit form cannot reach the file, the hash or the evidence number.** `UpdateEvidenceRequest` has no validation rules for those fields, so even a crafted request cannot change them.

**The model is named `InvestigationCase`, not `Case`.** `case` is a reserved word in PHP.

**The database table is `evidence`, not `evidences`.** The model sets `protected $table = 'evidence';` to make this explicit.

**Time zone is set explicitly to Asia/Dhaka** in `config/app.php`, and each evidence record also stores the time zone of its acquisition separately. In a forensic context a timestamp without a zone is not a timestamp.

---

## Branches

| Branch | Purpose |
| --- | --- |
| `main` | Reviewed and approved work |
| `module-1-evidence-intake` | Current development |

Each module is developed on its own branch and merged into `main` once complete and reviewed.
