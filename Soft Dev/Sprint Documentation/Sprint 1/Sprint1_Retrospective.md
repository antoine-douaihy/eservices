# Sprint 1 — Retrospective
**Epic: Authentication, Security & Roles**
**Date:** January 30, 2026
**Facilitator:** Person C
**Attendees:** Person A, Person B, Person C, Person D

---

## What Went Well ✅

- **2FA completed ahead of schedule** — Person D finished the OTP implementation a day early, allowing time to polish the UI and add resend functionality.
- **Database schema was solid on the first draft** — The team designed the `users` table collaboratively during grooming, which prevented schema migrations mid-sprint.
- **Google OAuth worked on the first integration attempt** — The Socialite library documentation was clear and the setup was smooth.
- **Daily standups kept the team aligned** — Short 10-minute check-ins each day helped surface blockers early (e.g., SMTP config issue caught on Day 2).
- **Code quality was consistent** — All PRs on Azure DevOps received at least one review comment and were merged cleanly.

---

## What Needs Improvement ⚠️

- **ID verification API documentation was unclear** — Person A spent nearly half a day deciphering the API response format. The integration worked but cost time.
- **SMTP configuration was not prepared before the sprint started** — Person C had to wait for email provider credentials on Day 1, delaying the password reset task.
- **No shared environment setup guide** — Each team member manually configured their `.env` file, leading to inconsistencies in local dev environments.
- **Error handling was added late** — API error states (timeout, invalid ID) were only handled near the end of the sprint, which should have been part of the original task definition.

---

## Action Items 🎯

| # | Action | Owner | Target |
|---|--------|-------|--------|
| 1 | Create a shared `ENV_SETUP.md` guide documenting all required `.env` keys and where to get them | Person B | Start of Sprint 2 |
| 2 | Add a shared API documentation wiki page in Azure DevOps for all third-party integrations | Person A | Sprint 2, Day 1 |
| 3 | Enforce mandatory PR review by at least one teammate before any branch is merged | All | Sprint 2 onward |
| 4 | Include error handling and fallback states explicitly in task acceptance criteria during grooming | Person C | Sprint 2 Grooming |
| 5 | Prepare all external credentials (API keys, OAuth secrets) before sprint kickoff | Person D | Sprint 2 Prep |

---

## Team Health Check

| Metric | Rating (1–5) |
|--------|-------------|
| Collaboration | ⭐⭐⭐⭐⭐ 5 |
| Communication | ⭐⭐⭐⭐ 4 |
| Technical confidence | ⭐⭐⭐⭐ 4 |
| Sprint planning quality | ⭐⭐⭐ 3 |
| Overall sprint satisfaction | ⭐⭐⭐⭐ 4 |

---

## Summary
Sprint 1 was successfully delivered with 100% of committed story points completed. The team built a strong authentication and security foundation. The main area to improve is pre-sprint preparation — credentials, environment setup, and error handling expectations should all be defined before sprint kickoff.
