# Manual Test Cases — Bonus Component
**Project:** E-Services Management Platform
**Course:** Web Development 2 — PROG322-EC20
**Prepared by:** Person C (Account Security), All Members (Review)
**Total Test Cases:** 20
**Execution Date:** February 18–19, 2026

---

## Test Case Format
Each test case includes: **ID**, **Title**, **Description**, **Preconditions**, **Steps**, **Expected Result**, **Actual Result**, **Status**, **Notes**.

---

## Module 1: Account Registration & Security (Person C)

### TC-001
| Field | Details |
|-------|---------|
| **Title** | Citizen registration with valid ID upload |
| **Description** | Verify a new citizen can register successfully when all fields are valid and a valid ID is uploaded. |
| **Preconditions** | User has not registered before. Valid PDF ID file available. |
| **Steps** | 1. Navigate to `/register` 2. Fill in: Name, Email, Password, Confirm Password 3. Upload a valid PDF ID document 4. Click "Register" |
| **Expected Result** | Account is created. User is redirected to the 2FA setup or citizen dashboard. Success message is shown. |
| **Actual Result** | Account created. Redirected to citizen dashboard. |
| **Status** | ✅ Pass |
| **Notes** | ID verification API returned citizen data correctly. |

---

### TC-002
| Field | Details |
|-------|---------|
| **Title** | Registration fails with duplicate email |
| **Description** | Verify the system rejects registration when the email is already in use. |
| **Preconditions** | `ahmad@test.com` is already registered. |
| **Steps** | 1. Navigate to `/register` 2. Enter `ahmad@test.com` as the email 3. Fill remaining fields 4. Click "Register" |
| **Expected Result** | Error: "The email has already been taken." Form is not submitted. |
| **Actual Result** | Validation error displayed. No new account created. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

### TC-003
| Field | Details |
|-------|---------|
| **Title** | Password reset email is sent and link works |
| **Description** | Verify the "Forgot Password" flow delivers a functional reset link via email. |
| **Preconditions** | User `citizen@test.com` exists. SMTP is configured. |
| **Steps** | 1. Navigate to `/forgot-password` 2. Enter `citizen@test.com` 3. Click "Send Reset Link" 4. Open email inbox 5. Click the reset link 6. Enter and confirm a new valid password 7. Submit |
| **Expected Result** | Email received within 60 seconds. Reset link opens `/reset-password` form. New password saves successfully. User can log in with new password. |
| **Actual Result** | Email received in ~12 seconds. Password reset successful. |
| **Status** | ✅ Pass |
| **Notes** | Reset link expires after 60 minutes. |

---

### TC-004
| Field | Details |
|-------|---------|
| **Title** | Password reset link is single-use |
| **Description** | Verify that a password reset link cannot be reused after it is consumed. |
| **Preconditions** | A valid reset link was sent and used once. |
| **Steps** | 1. Use the reset link to change password successfully 2. Click the same reset link again |
| **Expected Result** | Error: "This password reset token is invalid or has expired." |
| **Actual Result** | Error message shown. Access denied. |
| **Status** | ✅ Pass |
| **Notes** | Token invalidation works correctly. |

---

### TC-005
| Field | Details |
|-------|---------|
| **Title** | Weak password is rejected at registration |
| **Description** | Verify the system enforces strong password rules. |
| **Preconditions** | None. |
| **Steps** | 1. Navigate to `/register` 2. Enter password: `abc` (3 chars, no numbers, no special char) 3. Submit |
| **Expected Result** | Validation error: "Password must be at least 8 characters and include a number and a special character." |
| **Actual Result** | Error displayed. Form not submitted. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

## Module 2: Admin Panel

### TC-006
| Field | Details |
|-------|---------|
| **Title** | Admin creates a new government office |
| **Description** | Verify admin can create a government office with all required fields. |
| **Preconditions** | Admin is logged in. At least one municipality exists. |
| **Steps** | 1. Navigate to `/admin/offices/create` 2. Fill: name, address, municipality, working hours, contact 3. Drop a pin on the map 4. Click "Save" |
| **Expected Result** | Office is created. Appears in the office list. Map pin coordinates are stored correctly. |
| **Actual Result** | Office created and visible in list. Lat/lng stored. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

### TC-007
| Field | Details |
|-------|---------|
| **Title** | Admin deactivates a citizen account |
| **Description** | Verify deactivating an account prevents the user from logging in. |
| **Preconditions** | Citizen `citizen2@test.com` exists and is active. |
| **Steps** | 1. Admin navigates to User Directory 2. Finds `citizen2@test.com` 3. Clicks "Deactivate" and confirms 4. Attempt to log in as `citizen2@test.com` |
| **Expected Result** | Login is blocked. Message: "Your account has been suspended." |
| **Actual Result** | Login blocked. Suspension message shown. |
| **Status** | ✅ Pass |
| **Notes** | Reactivation also tested — restores access immediately. |

---

### TC-008
| Field | Details |
|-------|---------|
| **Title** | Analytics dashboard displays correct request counts |
| **Description** | Verify the Pie chart on the admin dashboard reflects actual request status distribution. |
| **Preconditions** | Database has seeded requests in various statuses. |
| **Steps** | 1. Log in as admin 2. Navigate to `/admin/dashboard` 3. View the Pie chart |
| **Expected Result** | Chart slices match the count of requests per status (Pending, In Review, Approved, etc.). |
| **Actual Result** | Chart matched manual count from database. |
| **Status** | ✅ Pass |
| **Notes** | Verified by comparing chart values to direct SQL count query. |

---

## Module 3: Office Staff Dashboard

### TC-009
| Field | Details |
|-------|---------|
| **Title** | Office staff can change request status to Approved |
| **Description** | Verify status change triggers correct DB update and citizen notification. |
| **Preconditions** | Request R001 exists with status "In Review." Office staff is logged in. |
| **Steps** | 1. Navigate to request R001 2. Change status to "Approved" 3. Click "Save" |
| **Expected Result** | Status updated to "Approved" in DB. Citizen receives an email notification. PDF certificate is auto-generated. |
| **Actual Result** | Status updated. Email sent. PDF generated and downloadable. |
| **Status** | ✅ Pass |
| **Notes** | Email arrived in under 20 seconds. |

---

### TC-010
| Field | Details |
|-------|---------|
| **Title** | Office staff can message a citizen about missing documents |
| **Description** | Verify in-app chat works between office staff and citizen. |
| **Preconditions** | Request R002 exists. Both users logged in on separate sessions. |
| **Steps** | 1. Office staff opens R002 and clicks "Chat" 2. Types: "Please upload your birth certificate." 3. Citizen opens the same request |
| **Expected Result** | Message appears instantly on the citizen's side without page refresh. Unread badge increments. |
| **Actual Result** | Message appeared in real time. Badge updated. |
| **Status** | ✅ Pass |
| **Notes** | Tested with Pusher and a secondary incognito window. |

---

### TC-011
| Field | Details |
|-------|---------|
| **Title** | PDF certificate contains correct citizen data |
| **Description** | Verify the auto-generated PDF is populated with correct citizen and service information. |
| **Preconditions** | Request R003 is in "Approved" status with associated citizen data. |
| **Steps** | 1. Staff sets request R003 to Approved 2. Citizen downloads the generated PDF |
| **Expected Result** | PDF contains: citizen full name, service name, office name, issue date, unique reference number. |
| **Actual Result** | All fields present and correct in PDF. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

## Module 4: Citizen Portal

### TC-012
| Field | Details |
|-------|---------|
| **Title** | Citizen submits a service request with document upload |
| **Description** | Verify the full multi-step application form submission. |
| **Preconditions** | Citizen is logged in. "Birth Certificate" service exists and requires 1 document. |
| **Steps** | 1. Browse to service 2. Click Apply 3. Complete all 3 wizard steps 4. Upload required PDF 5. Confirm and submit |
| **Expected Result** | Request created with status "Pending." QR code displayed on confirmation page. |
| **Actual Result** | Request created. QR code visible. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

### TC-013
| Field | Details |
|-------|---------|
| **Title** | Card payment is processed successfully via Stripe |
| **Description** | Verify end-to-end Stripe payment updates request status to Paid. |
| **Preconditions** | Citizen has a pending request. Stripe test mode active. |
| **Steps** | 1. Navigate to payment page 2. Enter Stripe test card `4242 4242 4242 4242` 3. Submit payment |
| **Expected Result** | Payment confirmed. Request status becomes "Paid." Confirmation page shown. |
| **Actual Result** | Payment processed. Status updated to Paid. |
| **Status** | ✅ Pass |
| **Notes** | Stripe test mode used. |

---

### TC-014
| Field | Details |
|-------|---------|
| **Title** | QR tracking page accessible without login |
| **Description** | Verify the public tracking page works in an unauthenticated session. |
| **Preconditions** | Request with UUID "abc-1234" exists. |
| **Steps** | 1. Open incognito window 2. Navigate to `/track/abc-1234` |
| **Expected Result** | Page loads showing service name, office, and current status. No login required. No private citizen data shown. |
| **Actual Result** | Public tracking page loaded correctly. No sensitive data exposed. |
| **Status** | ✅ Pass |
| **Notes** | Tested in both Chrome and Firefox incognito. |

---

### TC-015
| Field | Details |
|-------|---------|
| **Title** | Citizen can rate a completed service |
| **Description** | Verify the rating widget is visible and functional for completed requests. |
| **Preconditions** | Request R005 has status "Completed." Citizen is logged in. |
| **Steps** | 1. Navigate to `/citizen/requests/5` 2. Click the 5th star 3. Enter comment: "Fast and professional!" 4. Click Submit |
| **Expected Result** | Rating saved. Confirmation shown. Rating visible on office's public profile page. |
| **Actual Result** | Rating submitted and visible on office profile. |
| **Status** | ✅ Pass |
| **Notes** | Average rating recalculated automatically. |

---

## Module 5: Security & Access Control

### TC-016
| Field | Details |
|-------|---------|
| **Title** | 2FA invalid OTP is rejected |
| **Description** | Verify wrong OTP blocks login and does not create a session. |
| **Preconditions** | Admin account with 2FA enabled. |
| **Steps** | 1. Log in with valid admin credentials 2. On 2FA page, enter OTP: `000000` 3. Submit |
| **Expected Result** | Error: "Invalid or expired OTP." User remains on 2FA page. No session created. |
| **Actual Result** | Error displayed. Remained on 2FA page. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

### TC-017
| Field | Details |
|-------|---------|
| **Title** | Citizen cannot access office staff routes |
| **Description** | Verify RBAC middleware blocks cross-role access. |
| **Preconditions** | Citizen is logged in. |
| **Steps** | 1. Directly navigate to `/office/requests` while logged in as citizen |
| **Expected Result** | HTTP 403 Forbidden. Access denied message. |
| **Actual Result** | 403 page shown. |
| **Status** | ✅ Pass |
| **Notes** | Tested for all three cross-role combinations. |

---

### TC-018
| Field | Details |
|-------|---------|
| **Title** | Unauthenticated user is redirected to login |
| **Description** | Verify protected routes redirect guests to the login page. |
| **Preconditions** | No active session. |
| **Steps** | 1. Open a fresh browser tab 2. Navigate to `/citizen/dashboard` |
| **Expected Result** | Redirect to `/login`. Original URL stored for post-login redirect. |
| **Actual Result** | Redirected to login. After login, redirected to original URL. |
| **Status** | ✅ Pass |
| **Notes** | Laravel's `auth` middleware handles this natively. |

---

### TC-019
| Field | Details |
|-------|---------|
| **Title** | Declined crypto payment does not mark request as Paid |
| **Description** | Verify failed crypto payment does not update request status. |
| **Preconditions** | Pending request exists. |
| **Steps** | 1. Select crypto payment 2. Cancel or simulate a failed transaction 3. Check request status |
| **Expected Result** | Request status remains "Pending." Payment logged as "Failed" in payments table. |
| **Actual Result** | Status unchanged. Failure logged correctly. |
| **Status** | ✅ Pass |
| **Notes** | — |

---

### TC-020
| Field | Details |
|-------|---------|
| **Title** | Citizen cannot submit a request without uploading required documents |
| **Description** | Verify application wizard enforces document upload before proceeding. |
| **Preconditions** | Service requires 2 documents. Citizen is on Step 2 of the wizard. |
| **Steps** | 1. Skip document upload completely 2. Click "Next" to proceed to Step 3 |
| **Expected Result** | Validation error: "Please upload all required documents." Step 2 is not submitted. |
| **Actual Result** | Validation error shown. Stayed on Step 2. |
| **Status** | ✅ Pass |
| **Notes** | Tested for both 1 of 2 and 0 of 2 documents uploaded. |

---

## Summary

| Module | Total | Passed | Failed |
|--------|-------|--------|--------|
| Account Registration & Security | 5 | 5 | 0 |
| Admin Panel | 3 | 3 | 0 |
| Office Staff Dashboard | 3 | 3 | 0 |
| Citizen Portal | 4 | 4 | 0 |
| Security & Access Control | 5 | 5 | 0 |
| **Total** | **20** | **20** | **0** |

**Pass Rate: 100%**
