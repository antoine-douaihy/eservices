# Sprint 3 — Review
**Epic: Government Office Dashboard**
**Sprint Dates:** February 7, 2026 – February 13, 2026
**Review Date:** February 13, 2026
**Attendees:** Person A, Person B, Person C, Person D, Instructor

---

## Sprint Goal — Achieved ✅
The Government Office workspace is complete. Staff can define services, manage the full request lifecycle, auto-generate PDF certificates, and communicate with citizens via in-app messaging and email notifications.

---

## Completed Stories

| Story ID | User Story | Story Points | Status |
|----------|-----------|--------------|--------|
| US-013 | Service definition with price and required documents | 8 | ✅ Done |
| US-014 | Request pipeline with Kanban status management | 8 | ✅ Done |
| US-015 | Auto PDF certificate generation on approval | 8 | ✅ Done |
| US-016 | In-app messaging between staff and citizens | 8 | ✅ Done |
| US-017 | Email notification on request status change | 5 | ✅ Done |
| US-018 | Service categories CRUD | 3 | ✅ Done |

**Velocity: 40 / 40 story points delivered**

---

## Demo Summary

1. **Service Definition** — Person A demonstrated creating a "Birth Certificate" service under the "Civil Registry" category with a price of $15, 3-day processing time, and 2 required documents (National ID + Application Form). The service appeared instantly on the citizen browse page.
2. **Request Pipeline** — Person B demonstrated the Office Staff inbox with live request data. Changing a request from "Pending" to "In Review" updated the Kanban card in real time. Status filter tabs worked correctly.
3. **PDF Generation** — Person C approved a test request live. Within 2 seconds, the system generated a formatted PDF certificate with the office letterhead, citizen name, service name, issue date, and a unique reference number. The citizen could download it immediately.
4. **In-App Chat** — Two browser sessions were open simultaneously. A citizen sent a message about a missing document; the Office Staff received it instantly (no refresh required) and replied. The citizen received the reply with a notification badge.
5. **Email Notification** — A request status was changed to "Approved" and the team showed the email arriving in a test inbox within 15 seconds with the correct status and reference number.

---

## Accepted User Stories
- ✅ US-013 — Service Definition Engine
- ✅ US-014 — Request Pipeline
- ✅ US-015 — PDF Certificate Generator
- ✅ US-016 — In-App Messaging
- ✅ US-017 — Email Notifications
- ✅ US-018 — Service Categories

## Rejected / Deferred Stories
> None — all committed stories delivered.

---

## Stakeholder Feedback

| From | Feedback |
|------|----------|
| Instructor | PDF certificate looked professional. Suggested adding a QR code on the certificate itself linking to the tracking page. |
| Instructor | Chat feature was the highlight of the demo. Recommended also showing unread message count in the citizen dashboard. |
| Instructor | Email notification was well-formatted. Add an "Update your settings" unsubscribe footer for professionalism. |
| Team | QR on PDF and unread badge noted as Sprint 4 backlog items. |

---

## Key Decisions Made
- Real-time messaging implemented using **Laravel Echo + Pusher** (free tier, 200k messages/day — sufficient for demo).
- PDF template uses `barryvdh/laravel-dompdf` with an Arabic-compatible font for potential bilingual certificates.
- Status transitions enforced in the `RequestService` class — invalid transitions return a 422 Unprocessable Entity.
