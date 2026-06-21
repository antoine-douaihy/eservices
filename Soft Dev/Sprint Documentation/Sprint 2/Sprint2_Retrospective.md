# Sprint 2 — Retrospective
**Epic: Admin Controls & Operations**
**Date:** February 6, 2026
**Facilitator:** Person D
**Attendees:** Person A, Person B, Person C, Person D

---

## What Went Well ✅

- **Google Maps API integration was smooth** — Person C researched the API in advance (action item from Sprint 1) and completed the pin implementation on Day 2.
- **Shared ENV setup guide worked perfectly** — The `ENV_SETUP.md` created after Sprint 1 meant zero environment issues this sprint. All four members had matching setups from Day 1.
- **Chart.js charts delivered real visual value** — The analytics dashboard made the demo significantly more impressive and was praised by the instructor.
- **Office CRUD followed a clean reusable pattern** — Person A structured the controller and views so cleanly that Person B was able to reuse the pattern for the user directory with minimal effort.
- **PR review culture improved** — All 8 pull requests this sprint had at least one review before merge, up from sporadic reviews in Sprint 1.

---

## What Needs Improvement ⚠️

- **Chart data required significant seeding effort** — The database had no real data, so Person D had to write extensive seeders just for the demo. This should have been prepared earlier.
- **Municipality scoping was added late** — The requirement to scope offices to municipalities was underestimated during grooming and caused a schema adjustment mid-sprint.
- **Admin dashboard UI needed multiple revision rounds** — The layout was revised three times before everyone was satisfied. A wireframe or mockup before coding would have saved time.

---

## Action Items 🎯

| # | Action | Owner | Target |
|---|--------|-------|--------|
| 1 | Write and maintain database seeders for all major entities as features are built | Person A | Sprint 3, ongoing |
| 2 | Create a simple wireframe/sketch for any new page before starting front-end work | Person D | Sprint 3 Grooming |
| 3 | Add UI review checkpoint (30 min) midway through sprint before final polish | All | Sprint 3 onward |
| 4 | Integrate Chart.js date-range filter for the revenue chart | Person D | Sprint 4 backlog |
| 5 | Add Google Maps address search box to the office form | Person C | Sprint 4 backlog |

---

## Team Health Check

| Metric | Rating (1–5) |
|--------|-------------|
| Collaboration | ⭐⭐⭐⭐⭐ 5 |
| Communication | ⭐⭐⭐⭐⭐ 5 |
| Technical confidence | ⭐⭐⭐⭐ 4 |
| Sprint planning quality | ⭐⭐⭐⭐ 4 |
| Overall sprint satisfaction | ⭐⭐⭐⭐ 4 |

---

## Summary
Sprint 2 was another full delivery. The Admin panel is production-ready. Key improvements this sprint: environment setup, PR reviews, and third-party API research upfront. Main lesson learned: always prepare database seeders and UI wireframes before coding begins.
