# Sprint 4 — Review
**Epic: Citizen Experience & Transactions**
**Sprint Dates:** February 14, 2026 – February 20, 2026
**Review Date:** February 20, 2026
**Attendees:** Person A, Person B, Person C, Person D, Instructor

---

## Sprint Goal — Achieved ✅
The citizen portal is complete. The full service application, payment (card and crypto), QR tracking, and rating system are all live. The E2E test suite is written and all scripts pass.

---

## Completed Stories

| Story ID | User Story | Story Points | Status |
|----------|-----------|--------------|--------|
| US-019 | Multi-step citizen service application with nearest-office detection | 8 | ✅ Done |
| US-020 | Stripe card payment integration | 8 | ✅ Done |
| US-021 | Cryptocurrency payment with live exchange rate | 8 | ✅ Done |
| US-022 | QR code generation and public tracking page | 5 | ✅ Done |
| US-023 | Star rating and comment system for completed services | 3 | ✅ Done |
| US-024 | Full E2E test suite (5 scripts, all passing) | 8 | ✅ Done |

**Velocity: 40 / 40 story points delivered**

---

## Demo Summary

1. **Service Application Wizard** — Person A demonstrated the 3-step application form. The system automatically detected the nearest Civil Registry office based on the citizen's GPS location. All required documents were uploaded and validated successfully.
2. **Stripe Payment** — Person B ran a Stripe test card payment live. The PaymentIntent was created, the card was charged, and the request status updated to "Paid" within 3 seconds of confirmation.
3. **Crypto Payment** — Person C showed the crypto payment page with live BTC and USDT rates from CoinGecko. The USD amount was converted and displayed in real time. A test wallet address was generated and the transaction was manually confirmed.
4. **QR Tracking** — Person D opened the public `/track/{uuid}` page in an incognito window (no login). It displayed the service name, office, current status, and last updated timestamp. A physical QR code was scanned with a phone during the demo.
5. **Rating System** — A completed request showed the 5-star rating widget. Person D submitted a 4-star rating with a comment. The rating appeared on the office's public profile page with an updated average score.
6. **E2E Tests** — All 5 test scripts were run from the terminal during the demo with 100% pass rate displayed in the console output.

---

## Accepted User Stories
- ✅ US-019 — Service Application Workflow
- ✅ US-020 — Fiat Payment (Stripe)
- ✅ US-021 — Cryptocurrency Payment
- ✅ US-022 — QR Code Tracking
- ✅ US-023 — Star Rating System
- ✅ US-024 — E2E Test Suite

## Rejected / Deferred Stories
> None — all committed stories delivered.

---

## Stakeholder Feedback

| From | Feedback |
|------|----------|
| Instructor | Crypto payment was impressive and uncommon in student projects. |
| Instructor | QR scan on a real phone during the demo was a great touch — showed real-world usability. |
| Instructor | Suggested the rating system also notify the office staff when a new review is submitted. |
| Instructor | Overall: the platform is complete, professional, and exceeds expectations for a course project. |

---

## Final Platform Summary

| Module | Status |
|--------|--------|
| Authentication & 2FA | ✅ Complete |
| Role-Based Access Control | ✅ Complete |
| Admin Panel (offices, users, analytics) | ✅ Complete |
| Google Maps Integration | ✅ Complete |
| Office Service & Category Management | ✅ Complete |
| Request Pipeline & Status Management | ✅ Complete |
| PDF Certificate Auto-Generation | ✅ Complete |
| In-App Real-Time Chat | ✅ Complete |
| Email & Push Notifications | ✅ Complete |
| Citizen Application Wizard | ✅ Complete |
| Stripe Card Payment | ✅ Complete |
| Cryptocurrency Payment | ✅ Complete |
| QR Code Tracking (public) | ✅ Complete |
| Star Rating & Reviews | ✅ Complete |
| E2E Test Suite (5 scripts) | ✅ Complete |
