# Sprint 1 — Review
**Epic: Authentication, Security & Roles**
**Sprint Dates:** January 24, 2026 – January 30, 2026
**Review Date:** January 30, 2026
**Attendees:** Person A, Person B, Person C, Person D, Instructor

---

## Sprint Goal — Achieved ✅
All three user roles can register, authenticate, and access their respective dashboards. Role-based access control is fully enforced. 2FA is active for Admin and Office accounts.

---

## Completed Stories

| Story ID | User Story | Story Points | Status |
|----------|-----------|--------------|--------|
| US-001 | Citizen registration with ID upload and API verification | 8 | ✅ Done |
| US-002 | Role-based middleware protecting all routes | 5 | ✅ Done |
| US-003 | Hidden admin registration page | 3 | ✅ Done |
| US-004 | Profile management and password reset via email | 5 | ✅ Done |
| US-005 | Two-factor authentication for Admin and Office roles | 8 | ✅ Done |
| US-006 | Google social login via Laravel Socialite | 5 | ✅ Done |

**Velocity: 34 / 34 story points delivered**

---

## Demo Summary

The team demonstrated the full authentication lifecycle live:

1. **Citizen Registration** — Person A demonstrated the public signup form, ID upload (PDF and image both accepted), and the response returned from the external ID verification API populating the user's name and data automatically.
2. **Role Redirect** — After logging in, Admin was redirected to `/admin/dashboard`, Office Staff to `/office/dashboard`, and Citizen to `/citizen/dashboard`. Accessing the wrong dashboard via direct URL returned a 403 Forbidden page.
3. **2FA Flow** — Person D demonstrated 2FA for an Admin account: after entering credentials, the system sent a 6-digit OTP to the registered email. Invalid OTP showed an error; valid OTP completed login.
4. **Google Login** — Person D demonstrated the Google OAuth popup, successful callback, and session creation for a citizen account.
5. **Password Reset** — Person C demonstrated the "Forgot Password" flow: email received within seconds, reset link valid for 60 minutes, new password enforced validation rules.

---

## Accepted User Stories
- ✅ US-001 — Citizen Registration & ID Vault
- ✅ US-002 — RBAC & Middleware
- ✅ US-003 — Admin-only Registration
- ✅ US-004 — Profile & Password Reset
- ✅ US-005 — Two-Factor Authentication
- ✅ US-006 — Google Social Login

## Rejected / Deferred Stories
> None — all committed stories were delivered.

---

## Stakeholder Feedback

| From | Feedback |
|------|----------|
| Instructor | Clean login UI. Suggested adding clearer error messages when ID verification API fails or times out. |
| Instructor | Recommended logging failed 2FA attempts for security auditing. |
| Team | Agreed to add API error handling and fallback messaging in Sprint 1 hotfix or Sprint 2. |

---

## Key Decisions Made
- 2FA will use **email-based OTP** (not Google Authenticator) for simplicity and compatibility.
- ID verification API timeout will be caught gracefully — citizen can still register but flagged as "pending verification."
- Social login creates a citizen account by default; role elevation must go through Admin.
