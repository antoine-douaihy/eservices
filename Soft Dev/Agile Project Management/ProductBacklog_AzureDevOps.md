# Agile Project Management — Azure DevOps
**Project:** E-Services Management Platform
**Course:** Web Development 2 — PROG322-EC20
**Tool:** Azure DevOps Boards
**Methodology:** Scrum

---

## Project Configuration

### Team Members
| Member | Azure DevOps Username | Role |
|--------|-----------------------|------|
| Person A | person-a | Developer — Citizen & Service Flows |
| Person B | person-b | Developer — Admin & Payments |
| Person C | person-c | Developer — Office Dashboard & Geo |
| Person D | person-d | Developer — Auth, Notifications & QR |

### Repository
- **Platform:** Azure DevOps Git Repository
- **Branching Strategy:** Feature branches → `develop` → `main`
- **Branch naming:** `feature/US-XXX-short-description`
- **PR Policy:** Minimum 1 reviewer approval before merge

---

## Sprint Configuration

| Sprint | Name | Start Date | End Date | Capacity |
|--------|------|-----------|----------|----------|
| Sprint 1 | Authentication, Security & Roles | Jan 24, 2026 | Jan 30, 2026 | 34 pts |
| Sprint 2 | Admin Controls & Operations | Jan 31, 2026 | Feb 6, 2026 | 34 pts |
| Sprint 3 | Government Office Dashboard | Feb 7, 2026 | Feb 13, 2026 | 40 pts |
| Sprint 4 | Citizen Experience & Transactions | Feb 14, 2026 | Feb 20, 2026 | 40 pts |

---

## Complete Product Backlog

### EPIC 1: Authentication, Security & Roles

| ID | Title | Type | Priority | Points | Sprint | Assignee | Status |
|----|-------|------|----------|--------|--------|----------|--------|
| US-001 | Citizen registration with ID upload and API verification | User Story | Critical | 8 | Sprint 1 | Person A | Closed |
| US-002 | Role-based middleware (CheckRole) protecting all routes | User Story | Critical | 5 | Sprint 1 | Person B | Closed |
| US-003 | Admin-only hidden registration page | User Story | High | 3 | Sprint 1 | Person B | Closed |
| US-004 | Citizen profile management and password reset via SMTP | User Story | High | 5 | Sprint 1 | Person C | Closed |
| US-005 | Two-factor authentication (2FA) for Admin and Office roles | User Story | High | 8 | Sprint 1 | Person D | Closed |
| US-006 | Google Social Login via Laravel Socialite | User Story | Medium | 5 | Sprint 1 | Person D | Closed |
| **Task** | Create `users` migration: `role`, `id_path`, `2fa_secret`, `google_id` | Task | — | — | Sprint 1 | Person A | Closed |
| **Task** | Create `CheckRole` middleware and register in Kernel.php | Task | — | — | Sprint 1 | Person B | Closed |
| **Task** | Configure SMTP in `.env` and test password reset email | Task | — | — | Sprint 1 | Person C | Closed |
| **Task** | Integrate `laravel/socialite` with Google OAuth | Task | — | — | Sprint 1 | Person D | Closed |
| **Task** | Write `E2E-Testing/Citizen_Auth_Flow.js` | Task | — | — | Sprint 1 | Person A | Closed |
| **Task** | Document Sprint 1 Grooming | Task | — | — | Sprint 1 | Person B | Closed |
| **Task** | Document Sprint 1 Review & Retrospective | Task | — | — | Sprint 1 | Person D | Closed |
| **Task** | Draft 5 manual test cases for account recovery | Task | — | — | Sprint 1 | Person C | Closed |

---

### EPIC 2: Admin Controls & Operations

| ID | Title | Type | Priority | Points | Sprint | Assignee | Status |
|----|-------|------|----------|--------|--------|----------|--------|
| US-007 | Admin CRUD for government offices with municipality assignment | User Story | Critical | 8 | Sprint 2 | Person A | Closed |
| US-008 | Admin promotes users to Staff/Admin and assigns to offices | User Story | High | 5 | Sprint 2 | Person B | Closed |
| US-009 | Google Maps pin on office create/edit form with lat/lng storage | User Story | High | 8 | Sprint 2 | Person C | Closed |
| US-010 | Admin analytics dashboard (Chart.js: revenue, citizens, requests) | User Story | Medium | 5 | Sprint 2 | Person D | Closed |
| US-011 | Admin activate/deactivate any user account | User Story | High | 3 | Sprint 2 | Person B | Closed |
| US-012 | `offices` and `municipalities` tables with FK relationships | User Story | Critical | 5 | Sprint 2 | Person A | Closed |
| **Task** | Create `municipalities` migration and seeder | Task | — | — | Sprint 2 | Person A | Closed |
| **Task** | Create `offices` migration with `lat`, `lng`, `municipality_id` | Task | — | — | Sprint 2 | Person A | Closed |
| **Task** | Integrate Google Maps JS API + Geocoding API | Task | — | — | Sprint 2 | Person C | Closed |
| **Task** | Build Chart.js Pie and Bar charts in Admin dashboard | Task | — | — | Sprint 2 | Person D | Closed |
| **Task** | Add `is_active` column to users and block login if false | Task | — | — | Sprint 2 | Person B | Closed |
| **Task** | Document Sprint 2 Grooming, Review, Retrospective | Task | — | — | Sprint 2 | All | Closed |

---

### EPIC 3: Government Office Dashboard

| ID | Title | Type | Priority | Points | Sprint | Assignee | Status |
|----|-------|------|----------|--------|--------|----------|--------|
| US-013 | Office Staff defines services with price and required documents | User Story | Critical | 8 | Sprint 3 | Person A | Closed |
| US-014 | Office Staff manages request pipeline with status updates | User Story | Critical | 8 | Sprint 3 | Person B | Closed |
| US-015 | Auto-generate PDF certificate on request approval (dompdf) | User Story | High | 8 | Sprint 3 | Person C | Closed |
| US-016 | In-app messaging between staff and citizens per request | User Story | High | 8 | Sprint 3 | Person D | Closed |
| US-017 | Email notification to citizen on every request status change | User Story | High | 5 | Sprint 3 | Person D | Closed |
| US-018 | Service categories CRUD for office staff | User Story | Medium | 3 | Sprint 3 | Person A | Closed |
| **Task** | Create `services`, `service_categories`, `required_documents` tables | Task | — | — | Sprint 3 | Person A | Closed |
| **Task** | Create `requests` table (full schema) | Task | — | — | Sprint 3 | Person B | Closed |
| **Task** | Install `barryvdh/laravel-dompdf` and build PDF Blade template | Task | — | — | Sprint 3 | Person C | Closed |
| **Task** | Install Laravel Echo + Pusher and create `messages` table | Task | — | — | Sprint 3 | Person D | Closed |
| **Task** | Configure Laravel Queue for async email dispatch | Task | — | — | Sprint 3 | Person D | Closed |
| **Task** | Document Sprint 3 Grooming, Review, Retrospective | Task | — | — | Sprint 3 | All | Closed |

---

### EPIC 4: Citizen Experience & Transactions

| ID | Title | Type | Priority | Points | Sprint | Assignee | Status |
|----|-------|------|----------|--------|--------|----------|--------|
| US-019 | Multi-step citizen service application with nearest office detection | User Story | Critical | 8 | Sprint 4 | Person A | Closed |
| US-020 | Stripe card payment integration with webhook | User Story | Critical | 8 | Sprint 4 | Person B | Closed |
| US-021 | Cryptocurrency payment with live CoinGecko exchange rate | User Story | High | 8 | Sprint 4 | Person C | Closed |
| US-022 | QR code generation and public tracking page (no login) | User Story | High | 5 | Sprint 4 | Person D | Closed |
| US-023 | 1–5 star rating system for completed service requests | User Story | Medium | 3 | Sprint 4 | Person D | Closed |
| US-024 | Full E2E test suite (5 scripts) written and passing | User Story | High | 8 | Sprint 4 | All | Closed |
| **Task** | Build 3-step application wizard (select → upload → review) | Task | — | — | Sprint 4 | Person A | Closed |
| **Task** | Create `request_documents` and `payments` tables | Task | — | — | Sprint 4 | Person A | Closed |
| **Task** | Install `stripe/stripe-php` and build PaymentIntent flow | Task | — | — | Sprint 4 | Person B | Closed |
| **Task** | Integrate CoinGecko API with 5-min cache | Task | — | — | Sprint 4 | Person C | Closed |
| **Task** | Install `simplesoftwareio/simple-qrcode` and generate per request | Task | — | — | Sprint 4 | Person D | Closed |
| **Task** | Build public `/track/{uuid}` page | Task | — | — | Sprint 4 | Person D | Closed |
| **Task** | Create `ratings` table and 5-star UI component | Task | — | — | Sprint 4 | Person D | Closed |
| **Task** | Write `Citizen_Application_Flow.js` E2E script | Task | — | — | Sprint 4 | Person A | Closed |
| **Task** | Write `Fiat_Payment_Flow.js` E2E script | Task | — | — | Sprint 4 | Person B | Closed |
| **Task** | Write `Crypto_QR_Rating_Flow.js` E2E script | Task | — | — | Sprint 4 | Person C + D | Closed |
| **Task** | Document Sprint 4 Grooming, Review, Retrospective | Task | — | — | Sprint 4 | All | Closed |

---

## Velocity Summary

| Sprint | Committed | Delivered | Velocity |
|--------|-----------|-----------|----------|
| Sprint 1 | 34 | 34 | 100% |
| Sprint 2 | 34 | 34 | 100% |
| Sprint 3 | 40 | 40 | 100% |
| Sprint 4 | 40 | 40 | 100% |
| **Total** | **148** | **148** | **100%** |

---

## Azure DevOps Workflow

```
New → Active → Resolved → Closed
         ↓
      Blocked (if dependencies unmet)
```

- **New:** Item added to backlog, not yet started
- **Active:** In progress — assigned developer is working on it
- **Resolved:** Development complete — awaiting peer review / PR approval
- **Closed:** Reviewed, merged, and accepted in sprint review
- **Blocked:** Cannot proceed due to external dependency

---

## Definition of Done (DoD)
All items must satisfy the following before being marked **Closed**:
1. ✅ Feature implemented and self-tested by developer
2. ✅ Pull request created with description referencing the story ID
3. ✅ At least 1 peer review approval on the PR
4. ✅ No open Critical or High severity bugs related to this story
5. ✅ Feature demonstrated in sprint review session
6. ✅ Azure DevOps task status set to **Closed**
