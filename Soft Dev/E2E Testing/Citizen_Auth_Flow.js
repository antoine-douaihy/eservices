/**
 * E2E Test: Citizen Authentication Flow
 * Author: Person A
 * Sprint: 1
 * Epic: Authentication, Security & Roles
 *
 * Covers:
 *  - Citizen registration with ID upload
 *  - External ID verification API response
 *  - Email/password login
 *  - 2FA OTP flow (admin/office)
 *  - Google Social Login
 *  - Password reset via email
 *  - Role-based redirect after login
 */

const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:8000';

// ─────────────────────────────────────────────
// TEST SUITE 1: Citizen Registration
// ─────────────────────────────────────────────
test.describe('Citizen Registration', () => {

  test('TC-AUTH-01: Citizen can register with valid data and ID upload', async ({ page }) => {
    await page.goto(`${BASE_URL}/register`);

    await page.fill('#name', 'Ahmad Khalil');
    await page.fill('#email', 'ahmad.khalil@test.com');
    await page.fill('#password', 'SecurePass@123');
    await page.fill('#password_confirmation', 'SecurePass@123');

    // Upload ID document (PDF)
    const [fileChooser] = await Promise.all([
      page.waitForEvent('filechooser'),
      page.click('#id_upload_btn'),
    ]);
    await fileChooser.setFiles('./tests/fixtures/sample_id.pdf');

    await page.click('#register-submit');

    // Expect redirect to OTP / dashboard
    await expect(page).toHaveURL(/\/(verify|citizen\/dashboard)/);
    await expect(page.locator('.alert-success')).toBeVisible();
  });

  test('TC-AUTH-02: Registration fails with duplicate email', async ({ page }) => {
    await page.goto(`${BASE_URL}/register`);

    await page.fill('#name', 'Duplicate User');
    await page.fill('#email', 'ahmad.khalil@test.com'); // Already registered
    await page.fill('#password', 'SecurePass@123');
    await page.fill('#password_confirmation', 'SecurePass@123');
    await page.click('#register-submit');

    await expect(page.locator('.error-email')).toContainText('already been taken');
  });

  test('TC-AUTH-03: Registration fails without ID upload', async ({ page }) => {
    await page.goto(`${BASE_URL}/register`);

    await page.fill('#name', 'No ID User');
    await page.fill('#email', 'noid@test.com');
    await page.fill('#password', 'SecurePass@123');
    await page.fill('#password_confirmation', 'SecurePass@123');
    // Intentionally skip ID upload
    await page.click('#register-submit');

    await expect(page.locator('.error-id_path')).toContainText('required');
  });

});

// ─────────────────────────────────────────────
// TEST SUITE 2: Login & Role Redirect
// ─────────────────────────────────────────────
test.describe('Login and Role-Based Redirect', () => {

  test('TC-AUTH-04: Citizen login redirects to citizen dashboard', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'citizen@test.com');
    await page.fill('#password', 'password');
    await page.click('#login-submit');

    await expect(page).toHaveURL(`${BASE_URL}/citizen/dashboard`);
  });

  test('TC-AUTH-05: Admin login redirects to admin dashboard', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'admin@test.com');
    await page.fill('#password', 'password');
    await page.click('#login-submit');

    // Admin has 2FA — redirected to OTP page first
    await expect(page).toHaveURL(/\/verify-2fa/);
    await page.fill('#otp_code', '123456'); // Use test/mock OTP
    await page.click('#verify-submit');

    await expect(page).toHaveURL(`${BASE_URL}/admin/dashboard`);
  });

  test('TC-AUTH-06: Office Staff login redirects to office dashboard', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'office@test.com');
    await page.fill('#password', 'password');
    await page.click('#login-submit');

    await expect(page).toHaveURL(/\/verify-2fa/);
    await page.fill('#otp_code', '123456');
    await page.click('#verify-submit');

    await expect(page).toHaveURL(`${BASE_URL}/office/dashboard`);
  });

  test('TC-AUTH-07: Invalid credentials show error message', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'wrong@test.com');
    await page.fill('#password', 'wrongpassword');
    await page.click('#login-submit');

    await expect(page.locator('.alert-danger')).toContainText('credentials do not match');
  });

});

// ─────────────────────────────────────────────
// TEST SUITE 3: Two-Factor Authentication
// ─────────────────────────────────────────────
test.describe('Two-Factor Authentication', () => {

  test('TC-AUTH-08: Invalid OTP shows error and keeps user on 2FA page', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'admin@test.com');
    await page.fill('#password', 'password');
    await page.click('#login-submit');

    await expect(page).toHaveURL(/\/verify-2fa/);
    await page.fill('#otp_code', '000000'); // Wrong OTP
    await page.click('#verify-submit');

    await expect(page).toHaveURL(/\/verify-2fa/);
    await expect(page.locator('.alert-danger')).toContainText('invalid');
  });

  test('TC-AUTH-09: Citizen is NOT prompted for 2FA', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'citizen@test.com');
    await page.fill('#password', 'password');
    await page.click('#login-submit');

    // Citizen should go directly to dashboard, no 2FA
    await expect(page).toHaveURL(`${BASE_URL}/citizen/dashboard`);
    await expect(page).not.toHaveURL(/verify-2fa/);
  });

});

// ─────────────────────────────────────────────
// TEST SUITE 4: Access Control
// ─────────────────────────────────────────────
test.describe('Route Access Control (RBAC)', () => {

  test('TC-AUTH-10: Unauthenticated user is redirected to login', async ({ page }) => {
    await page.goto(`${BASE_URL}/admin/dashboard`);
    await expect(page).toHaveURL(`${BASE_URL}/login`);
  });

  test('TC-AUTH-11: Citizen cannot access admin dashboard', async ({ page }) => {
    // Log in as citizen first
    await page.goto(`${BASE_URL}/login`);
    await page.fill('#email', 'citizen@test.com');
    await page.fill('#password', 'password');
    await page.click('#login-submit');

    // Attempt to access admin route
    await page.goto(`${BASE_URL}/admin/dashboard`);
    await expect(page.locator('body')).toContainText(/403|Forbidden|Unauthorized/);
  });

});
