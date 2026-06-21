# Sprint 1 — Grooming (Backlog Refinement)
**Epic: Authentication, Security & Roles**
**Sprint Dates:** January 24, 2026 – January 30, 2026
**Facilitator:** Person B
**Attendees:** Person A, Person B, Person C, Person D

---

## Sprint Goal
Establish the complete authentication and security foundation for the platform. By the end of this sprint, all three user roles (Admin, Municipality Staff, Citizen) must be able to register, log in, and be redirected to their respective dashboards with proper access control enforced.

---

## Backlog Items Reviewed & Estimated

| Story ID | User Story | Assigned To | Priority | Story Points | Status |
|----------|-----------|-------------|----------|--------------|--------|
| US-001 | As a Citizen, I want to register using email/password and upload my ID so my identity is verified. | Person A | Critical | 8 | Selected |
| US-002 | As a system, I need role-based middleware to protect routes so only authorized users access specific areas. | Person B | Critical | 5 | Selected |
| US-003 | As an Admin, I want a hidden registration page so only known admins can create admin accounts. | Person B | High | 3 | Selected |
| US-004 | As a user, I want to update my profile and recover my password via email so I can maintain my account. | Person C | High | 5 | Selected |
| US-005 | As an Admin/Office Staff, I want 2FA so my account is protected with an extra security layer. | Person D | High | 8 | Selected |
| US-006 | As a Citizen, I want to log in with my Google account so I can access the platform quickly. | Person D | Medium | 5 | Selected |

**Total Committed Points: 34**

---

## Task Breakdown per Person

### Person A — Citizen Registration & ID Vault
- [ ] Create public `/register` page (Blade view + form validation)
- [ ] Handle PDF/Image file upload for national ID
- [ ] Store file path in `id_path` column in `users` table
- [ ] Add `role` column (default: `citizen`) to `users` migration
- [ ] Call external ID verification API and store returned citizen data
- [ ] Write `E2E-Testing/Citizen_Auth_Flow.js` test script

### Person B — RBAC & Middleware
- [ ] Create `CheckRole` middleware and register it in `Kernel.php`
- [ ] Protect `/admin/*`, `/office/*`, `/citizen/*` routes with middleware
- [ ] Build hidden `/admin/register` route not accessible from public nav
- [ ] Update `LoginController` to redirect by role after authentication
- [ ] Document this Sprint 1 Grooming file

### Person C — Account Security & Profile Hub
- [ ] Build `/profile` page (update name, email, phone, password)
- [ ] Configure SMTP in `.env` for password reset email
- [ ] Implement Laravel's built-in `Password::sendResetLink()` flow
- [ ] Add strong password validation rules (min 8 chars, special char, number)
- [ ] Draft 5 manual test cases for account recovery flow

### Person D — Advanced Gateway (Socialite & 2FA)
- [ ] Install and configure `laravel/socialite`
- [ ] Add Google OAuth credentials to `.env`
- [ ] Build `/auth/google` and `/auth/google/callback` routes
- [ ] Implement 2FA using email-based OTP or Google Authenticator (for Admin + Office)
- [ ] Write Sprint 1 Review and Retrospective markdown files

---

## Database Changes This Sprint

| Table | Column | Type | Notes |
|-------|--------|------|-------|
| `users` | `role` | `enum('admin','office','citizen')` | Default: `citizen` |
| `users` | `id_path` | `varchar(255)` | Nullable, path to uploaded ID |
| `users` | `two_factor_secret` | `varchar(255)` | Nullable, for 2FA |
| `users` | `google_id` | `varchar(255)` | Nullable, for social login |

---

## Definition of Done
- [ ] All assigned tasks completed and pushed to feature branch
- [ ] Pull request reviewed and approved by at least one teammate on Azure DevOps
- [ ] No critical bugs open on the sprint board
- [ ] Feature demonstrated locally before sprint review
- [ ] Azure DevOps tasks moved to **Closed**

---

## Risks & Dependencies
- External ID verification API credentials must be obtained before Person A can complete the upload logic.
- Google OAuth requires a configured Google Cloud project — Person D must set this up on Day 1.
- SMTP configuration depends on access to the team's email provider credentials.
