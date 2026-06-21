# Sprint 2 — Review
**Epic: Admin Controls & Operations**
**Sprint Dates:** January 31, 2026 – February 6, 2026
**Review Date:** February 6, 2026
**Attendees:** Person A, Person B, Person C, Person D, Instructor

---

## Sprint Goal — Achieved ✅
The Admin control panel is fully operational. Offices can be created with geographic pins, staff can be promoted and assigned, and the analytics dashboard visualizes live platform data.

---

## Completed Stories

| Story ID | User Story | Story Points | Status |
|----------|-----------|--------------|--------|
| US-007 | Admin office CRUD with municipality assignment | 8 | ✅ Done |
| US-008 | Staff promotion and office assignment | 5 | ✅ Done |
| US-009 | Google Maps pin integration for office locations | 8 | ✅ Done |
| US-010 | Analytics dashboard with Chart.js charts | 5 | ✅ Done |
| US-011 | Account activate/deactivate toggle | 3 | ✅ Done |
| US-012 | Offices and municipalities database schema | 5 | ✅ Done |

**Velocity: 34 / 34 story points delivered**

---

## Demo Summary

1. **Office CRUD** — Person A created a new government office ("Tripoli Civil Registry"), assigned it to the "Liban Nord" municipality, and demonstrated edit and delete flows. Validation errors displayed correctly for missing fields.
2. **Google Maps Integration** — Person C demonstrated the map pin on the office creation form. Dragging the pin auto-filled the latitude, longitude, and address fields. The saved location rendered correctly on the citizen-facing office page.
3. **Staff Management** — Person B promoted a test citizen account to Office Staff and assigned them to the newly created office. The user's role was updated immediately and redirected to the office dashboard on next login.
4. **Account Deactivation** — Person B deactivated a test account and confirmed the login attempt was blocked with a "Your account has been suspended" message. Reactivation restored access instantly.
5. **Analytics Dashboard** — Person D demonstrated the live dashboard with a Pie chart showing request status distribution and a Bar chart showing revenue per office. KPI cards showed real counts from the seeded database.

---

## Accepted User Stories
- ✅ US-007 — Government Office CRUD
- ✅ US-008 — Staff & Role Management
- ✅ US-009 — Google Maps Integration
- ✅ US-010 — Analytics Dashboard
- ✅ US-011 — Account Activate/Deactivate
- ✅ US-012 — Database Schema

## Rejected / Deferred Stories
> None — all committed stories delivered.

---

## Stakeholder Feedback

| From | Feedback |
|------|----------|
| Instructor | Charts look great. Requested that the revenue chart support a custom date range filter, not just "last 30 days." |
| Instructor | Google Maps pin drag was smooth and impressive. Suggested adding a search bar to the map to find locations by address. |
| Team | Agreed to add date range filter and map search box as backlog items for Sprint 4 polish. |

---

## Key Decisions Made
- The "last admin lockout" guard was implemented: the system prevents demoting or deactivating the only remaining admin account.
- Chart.js was loaded via CDN to avoid bloating the Laravel asset pipeline.
- Office latitude/longitude stored with `decimal(10,8)` precision for accurate mapping.
