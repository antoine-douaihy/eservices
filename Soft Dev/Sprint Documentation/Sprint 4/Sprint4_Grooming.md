# Sprint 4 — Grooming (Backlog Refinement)
**Epic: Citizen Experience & Transactions**
**Sprint Dates:** February 14, 2026 – February 20, 2026
**Facilitator:** Person B
**Attendees:** Person A, Person B, Person C, Person D

---

## Sprint Goal
Deliver the complete citizen-facing portal. Citizens must be able to browse services, submit multi-step applications, pay via card or cryptocurrency, track requests via QR code, and rate completed services. All E2E tests must be written and passing before the final presentation.

---

## Backlog Items Reviewed & Estimated

| Story ID | User Story | Assigned To | Priority | Story Points | Status |
|----------|-----------|-------------|----------|--------------|--------|
| US-019 | As a Citizen, I want a multi-step form to apply for a service, upload documents, and connect to the nearest office. | Person A | Critical | 8 | Selected |
| US-020 | As a Citizen, I want to pay for services online via credit/debit card (Stripe). | Person B | Critical | 8 | Selected |
| US-021 | As a Citizen, I want to pay via cryptocurrency with a live exchange rate shown. | Person C | High | 8 | Selected |
| US-022 | As a Citizen, I want a QR code for each request and a public tracking page requiring no login. | Person D | High | 5 | Selected |
| US-023 | As a Citizen, I want to rate a service 1–5 stars and leave a comment after my request is completed. | Person D | Medium | 3 | Selected |
| US-024 | As a system, all major user flows must be covered by E2E test scripts. | All | High | 8 | Selected |

**Total Committed Points: 40**

---

## Task Breakdown per Person

### Person A — Service Application Workflow
- [ ] Build the multi-step wizard (Step 1: select service → Step 2: fill details + upload documents → Step 3: review + pay)
- [ ] Use Google Maps Geocoding API to detect the citizen's nearest office offering the selected service
- [ ] Validate document upload: accept PDF, JPG, PNG — max 5MB each
- [ ] Create `request_documents` table to store uploaded file paths per request
- [ ] Show application confirmation page with request ID and QR code

### Person B — Fiat Payment Integration (Stripe)
- [ ] Install `stripe/stripe-php` via Composer
- [ ] Build `/payment/{request}` page with Stripe Elements card input
- [ ] Create Stripe PaymentIntent on page load and confirm on submission
- [ ] Handle webhook: `payment_intent.succeeded` → update `requests.payment_status` to `paid`
- [ ] Show payment success/failure confirmation page
- [ ] Store `stripe_payment_id` in the `payments` table

### Person C — Cryptocurrency Payment Gateway
- [ ] Integrate a crypto price API (e.g., CoinGecko free API) to fetch real-time BTC/ETH/USDT rates
- [ ] Display USD equivalent amount in selected crypto on the payment page
- [ ] Generate or simulate a wallet address for the citizen to pay to
- [ ] Log `crypto_currency`, `crypto_amount`, `wallet_address`, `tx_hash` in `payments` table
- [ ] Mark request as paid after transaction confirmation (manual confirm for demo; webhook for production)

### Person D — QR Tracking & Rating System
- [ ] Install `simplesoftwareio/simple-qrcode` package
- [ ] Generate a unique QR code URL (`/track/{uuid}`) for each new request
- [ ] Build public `/track/{uuid}` page: shows office name, service name, status, last updated (no login)
- [ ] Build the 1–5 star rating component (CSS + JS, no external library)
- [ ] Create `ratings` table: `request_id`, `citizen_id`, `office_id`, `stars`, `comment`
- [ ] Show rating form only on requests with status = `Completed`
- [ ] Display average rating and reviews on the office's public profile page

---

## E2E Test Scripts Assignment
- Person A: `Citizen_Application_Flow.js` (multi-step form, document upload)
- Person B: `Fiat_Payment_Flow.js` (Stripe sandbox card payment)
- Person C: `Crypto_Payment_Flow.js` (crypto exchange rate + payment simulation)
- Person D: `QR_Tracking_Flow.js` + `Rating_Flow.js`
- Shared: `Citizen_Auth_Flow.js` already written in Sprint 1

---

## Database Changes This Sprint

| Table | Column | Type | Notes |
|-------|--------|------|-------|
| `request_documents` | `request_id`, `label`, `file_path` | FK + varchar | New table |
| `payments` | `request_id`, `method`, `amount`, `status`, `stripe_payment_id`, `crypto_*` | Mixed | New table |
| `ratings` | `request_id`, `citizen_id`, `stars`, `comment` | Standard | New table |
| `requests` | `qr_uuid` | `uuid` | For public tracking URL |

---

## Definition of Done
- [ ] Citizen can complete the full application → pay (card or crypto) → receive QR code flow
- [ ] Public tracking page accessible without login
- [ ] Rating submitted after request completion and visible on office page
- [ ] All 5 E2E scripts written and passing
- [ ] All Azure DevOps tasks closed
- [ ] Final demo rehearsal completed

---

## Dependencies & Risks
- Stripe requires a test account with webhook endpoint configured (use `stripe listen` CLI for local dev).
- CoinGecko free API rate limit: 30 calls/minute — add caching (5-minute TTL) to avoid hitting the limit.
- QR code UUID must be generated at request creation time (migration needs `qr_uuid` column with default `uuid()`).
