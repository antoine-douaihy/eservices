# Manual Test Cases — Account Recovery
**Epic 1 | Person C | E-Services Platform**

---

## TC-01: Valid Email Triggers Reset Code Email

**Precondition:** User account exists with email `test@example.com`

**Steps:**
1. Navigate to `/forgot-password`
2. Enter `test@example.com` in the email field
3. Click **Send Code**

**Expected Result:**
- Page redirects to `/verify-code`
- User receives an email with a 4-digit code within 1 minute
- `reset_code` column in `users` table is populated
- `reset_code_expires_at` is set to 10 minutes from now

**Pass / Fail:** ___

---

## TC-02: Invalid Email Shows Validation Error

**Precondition:** None

**Steps:**
1. Navigate to `/forgot-password`
2. Enter `nonexistent@fake.com` (email not in database)
3. Click **Send Code**

**Expected Result:**
- Page does NOT redirect
- Validation error shown: email not found
- No email is sent
- `reset_code` table unchanged

**Pass / Fail:** ___

---

## TC-03: Correct Code Proceeds to Password Reset

**Precondition:** Valid reset code has been sent to `test@example.com`

**Steps:**
1. Navigate to `/verify-code`
2. Enter the exact 4-digit code received in the email
3. Click **Verify Code**

**Expected Result:**
- Redirects to `/reset-password-code`
- No error message shown
- Session contains `reset_email`

**Pass / Fail:** ___

---

## TC-04: Expired Code Shows Error

**Precondition:** A reset code was sent more than 10 minutes ago

**Steps:**
1. Navigate to `/verify-code`
2. Enter the old (expired) 4-digit code
3. Click **Verify Code**

**Expected Result:**
- Page does NOT redirect
- Error shown: "Invalid or expired code. Please try again."
- User is not granted access to reset password

**Pass / Fail:** ___

---

## TC-05: Weak Password Rejected on Reset

**Precondition:** Valid code has been verified, user is on `/reset-password-code`

**Steps:**
1. Enter `password` in the New Password field
2. Enter `password` in Confirm Password
3. Click **Reset Password**

**Expected Result:**
- Form does not submit
- Validation errors shown for missing: uppercase letter, number, special character
- Password is NOT updated in the database

**Pass / Fail:** ___

---

## TC-06: Strong Password Resets Successfully

**Precondition:** Valid code has been verified, user is on `/reset-password-code`

**Steps:**
1. Enter `Secure@123` in the New Password field
2. Enter `Secure@123` in Confirm Password
3. Click **Reset Password**

**Expected Result:**
- Redirects to `/login` with success message: "Password reset successfully! Please login."
- User can log in with `Secure@123`
- `reset_code` and `reset_code_expires_at` are `null` in the database

**Pass / Fail:** ___
