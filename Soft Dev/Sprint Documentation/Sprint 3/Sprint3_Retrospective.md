# Sprint 3 — Retrospective
**Epic: Government Office Dashboard**
**Date:** February 13, 2026
**Facilitator:** Person A
**Attendees:** Person A, Person B, Person C, Person D

---

## What Went Well ✅

- **PDF generation was faster to implement than expected** — `dompdf` was straightforward and the template came together quickly using an existing Blade view.
- **Real-time chat was the most impressive feature so far** — Laravel Echo + Pusher worked on the first try. The team was proud to show two live browser sessions communicating.
- **Database seeders from Sprint 2 action item paid off** — The team had realistic test data ready from Day 1 of Sprint 3, making development and testing much faster.
- **Highest velocity sprint yet (40 points)** — The team's growing familiarity with the Laravel patterns built in Sprints 1–2 made Sprint 3 significantly more productive.
- **Wireframes drawn before coding** — Following the Sprint 2 retrospective action, quick sketches were made for the inbox and chat UI, reducing revision rounds from 3 to 1.

---

## What Needs Improvement ⚠️

- **Pusher setup caused a 4-hour delay** — The free Pusher account required email verification and key setup. A self-hosted fallback (soketi) should have been prepared.
- **PDF accessibility wasn't considered** — The generated PDF has no alt text or metadata for screen readers. Minor but worth noting.
- **Email queue wasn't configured initially** — Notifications were sent synchronously at first, which would block the response for 2–3 seconds. Queue workers were added mid-sprint.
- **E2E tests are falling behind** — The team has been building features without writing corresponding E2E scripts. This technical debt must be addressed in Sprint 4.

---

## Action Items 🎯

| # | Action | Owner | Target |
|---|--------|-------|--------|
| 1 | Set up `soketi` as a local Pusher-compatible fallback for development | Person D | Sprint 4, Day 1 |
| 2 | Configure Laravel Queue worker (`database` driver) and document in ENV guide | Person C | Sprint 4, Day 1 |
| 3 | Dedicate 1 full day in Sprint 4 to writing E2E test scripts for Sprints 1–3 flows | All | Sprint 4, Day 3 |
| 4 | Add QR code to PDF certificate (from instructor feedback) | Person D | Sprint 4 backlog |
| 5 | Add unread message badge to citizen dashboard | Person B | Sprint 4 backlog |

---

## Team Health Check

| Metric | Rating (1–5) |
|--------|-------------|
| Collaboration | ⭐⭐⭐⭐⭐ 5 |
| Communication | ⭐⭐⭐⭐⭐ 5 |
| Technical confidence | ⭐⭐⭐⭐⭐ 5 |
| Sprint planning quality | ⭐⭐⭐⭐ 4 |
| Overall sprint satisfaction | ⭐⭐⭐⭐⭐ 5 |

---

## Summary
Sprint 3 was the most productive sprint yet. The Office Dashboard is feature-complete and the real-time chat and PDF automation were standout achievements. The only technical debt to address in Sprint 4 is the E2E test suite and a few polish items from instructor feedback.
