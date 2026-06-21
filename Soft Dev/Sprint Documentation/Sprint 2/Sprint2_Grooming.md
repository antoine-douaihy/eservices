# Sprint 2 — Grooming (Backlog Refinement)
**Epic: Admin Controls & Operations**
**Sprint Dates:** January 31, 2026 – February 6, 2026
**Facilitator:** Person A
**Attendees:** Person A, Person B, Person C, Person D

---

## Sprint Goal
Build the full Admin control panel. By end of sprint, the Admin can create and manage government offices with geographic pinning, manage staff roles and assignments, and view platform-wide analytics on a visual dashboard.

---

## Backlog Items Reviewed & Estimated

| Story ID | User Story | Assigned To | Priority | Story Points | Status |
|----------|-----------|-------------|----------|--------------|--------|
| US-007 | As an Admin, I want to create, edit, and delete government offices assigned to municipalities. | Person A | Critical | 8 | Selected |
| US-008 | As an Admin, I want to promote users to Office Staff or Admin and assign them to offices. | Person B | High | 5 | Selected |
| US-009 | As an Admin, I want to pin office locations via Google Maps so citizens can find offices geographically. | Person C | High | 8 | Selected |
| US-010 | As an Admin, I want a dashboard with charts showing revenue, citizen count, and request status distribution. | Person D | Medium | 5 | Selected |
| US-011 | As an Admin, I want to activate or deactivate any user account on the platform. | Person B | High | 3 | Selected |
| US-012 | As a system, offices and municipalities tables must be structured with proper foreign key relationships. | Person A | Critical | 5 | Selected |

**Total Committed Points: 34**

---

## Task Breakdown per Person

### Person A — Government Office CRUD
- [ ] Create `offices` table migration: `name`, `address`, `lat`, `lng`, `municipality_id`, `working_hours`, `contact_info`
- [ ] Create `municipalities` table with foreign key to `offices`
- [ ] Build Admin CRUD interface for offices (index, create, edit, delete)
- [ ] Validate form inputs server-side (name required, lat/lng numeric)
- [ ] Scope offices to their assigned municipality

### Person B — Staff & Role Management
- [ ] Build "User Directory" page listing all users with their role and assigned office
- [ ] Add "Promote" action: change a citizen's role to `office` or `admin`
- [ ] Build office-staff assignment: link a staff member's `user_id` to an `office_id`
- [ ] Build account activate/deactivate toggle with confirmation modal
- [ ] Ensure deactivated users cannot log in (check `is_active` flag in login logic)

### Person C — Geographic Mapping (Google Maps API)
- [ ] Add Google Maps JavaScript API script to the office create/edit form
- [ ] Implement map pin drag-and-drop that auto-fills latitude and longitude fields
- [ ] Display the office pin on a read-only map on the citizen-facing office detail page
- [ ] Store `lat` and `lng` in the database (decimal precision: 8 decimal places)
- [ ] Add reverse geocoding to auto-fill the address field when pin is dropped

### Person D — Platform Analytics Dashboard
- [ ] Integrate Chart.js via CDN into the Admin dashboard Blade view
- [ ] Build Pie chart: request status distribution (Pending / In Review / Approved / Rejected / Completed)
- [ ] Build Bar chart: total revenue per office (last 30 days)
- [ ] Build KPI cards: Total Citizens, Total Offices, Total Requests, Total Revenue
- [ ] Aggregate data via Eloquent queries in `AdminDashboardController`

---

## Database Changes This Sprint

| Table | Column | Type | Notes |
|-------|--------|------|-------|
| `municipalities` | `id`, `name`, `region` | Standard | New table |
| `offices` | `municipality_id` | `FK → municipalities.id` | |
| `offices` | `lat`, `lng` | `decimal(10,8)` | Google Maps coordinates |
| `users` | `office_id` | `FK → offices.id` | Nullable, for office staff |
| `users` | `is_active` | `boolean` | Default: true |

---

## Definition of Done
- [ ] Admin can fully manage offices with map pins saved correctly
- [ ] Role promotion and deactivation tested with at least 2 test accounts
- [ ] Charts display real data from the database (not hardcoded)
- [ ] All PRs reviewed and merged to `develop` branch on Azure DevOps
- [ ] No open High/Critical bugs on the sprint board

---

## Dependencies & Risks
- Google Maps API key must be enabled for both JavaScript API and Geocoding API.
- Chart.js data depends on some existing request records — seed the database with sample data for demo purposes.
- Role promotion logic must not allow demoting the last Admin (guard against lockout).
