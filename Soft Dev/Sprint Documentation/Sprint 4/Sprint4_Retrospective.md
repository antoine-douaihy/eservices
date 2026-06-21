# Sprint 4 — Retrospective
**Epic: Citizen Experience & Transactions**
**Date:** February 20, 2026
**Facilitator:** Person C
**Attendees:** Person A, Person B, Person C, Person D

---

## What Went Well ✅

- **E2E tests were completed on schedule** — Following Sprint 3's action item, a full day was dedicated to writing test scripts. All 5 scripts were done by Day 4, leaving time for debugging.
- **Stripe integration was clean** — Person B followed Stripe's official Laravel guide and the webhook handler worked correctly in the sandbox environment on the first attempt.
- **Crypto payment impressed the instructors** — This was the most technically ambitious feature of the project and it paid off in terms of evaluation impact.
- **QR scan demo on a real phone was a highlight** — Simple idea but very effective at showing real-world usability.
- **Best code quality of all sprints** — Peer code reviews caught 3 logic bugs before they reached the demo. The team's review culture had genuinely improved across 4 sprints.
- **Full platform delivered on time** — 139 story points, 4 epics, 24 user stories — all completed.

---

## What Needs Improvement ⚠️

- **E2E tests should have been written progressively** — Catching up on 3 sprints of test writing in one day was stressful. Tests should be written alongside features.
- **Crypto payment confirmation is manual for demo** — In production, this would need a real blockchain webhook or payment processor. The manual confirm is a known limitation.
- **Nearest office detection has edge cases** — If a citizen is far from all offices, the detection falls back to the first office in the database, which is not ideal UX.
- **Rating notification to office staff was not implemented** — Noted in Sprint 4 Review feedback but ran out of time. Would be a quick addition post-project.

---

## Action Items 🎯 (Post-Project Improvements)

| # | Action | Owner | Notes |
|---|--------|-------|-------|
| 1 | Write E2E tests alongside each feature in future projects | All | Process improvement for next course |
| 2 | Replace manual crypto confirm with a real blockchain webhook | Person C | Post-project enhancement |
| 3 | Add fallback UX for "no nearby office found" scenario | Person A | Post-project enhancement |
| 4 | Notify office staff on new rating submission | Person D | Post-project enhancement |
| 5 | Add appointment cancellation / rescheduling | Person B | Post-project enhancement |

---

## Team Health Check — Final Sprint

| Metric | Rating (1–5) |
|--------|-------------|
| Collaboration | ⭐⭐⭐⭐⭐ 5 |
| Communication | ⭐⭐⭐⭐⭐ 5 |
| Technical confidence | ⭐⭐⭐⭐⭐ 5 |
| Sprint planning quality | ⭐⭐⭐⭐⭐ 5 |
| Overall sprint satisfaction | ⭐⭐⭐⭐⭐ 5 |

---

## Final Project Retrospective

### What we built
A full-stack E-Services Management Platform built with Laravel + MySQL, featuring:
- 3 user roles with RBAC and 2FA
- Full Admin, Office, and Citizen dashboards
- Card and cryptocurrency payments
- Real-time chat and notifications
- PDF auto-generation and QR tracking
- A passing E2E test suite

### What we learned as a team
1. **Agile works** — Breaking the project into 4 focused sprints with clear goals prevented scope creep and kept the team aligned throughout.
2. **Environment setup documentation saves hours** — The `ENV_SETUP.md` created after Sprint 1 eliminated all local dev issues for the rest of the project.
3. **Research third-party integrations before the sprint starts** — Every time we read the docs in advance, the integration went smoothly. Every time we didn't, we lost time.
4. **Code reviews make the final product better** — The bugs caught in Sprint 4 reviews would have embarrassed us during the final demo.
5. **Write tests as you build** — The E2E test scramble in Sprint 4 was avoidable and stressful. In the next project, tests are written with the feature, not after.
