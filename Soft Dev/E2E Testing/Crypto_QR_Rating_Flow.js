/**
 * E2E Test: Cryptocurrency Payment Flow
 * Author: Person C
 * Sprint: 4
 * Epic: Citizen Experience & Transactions
 *
 * Covers:
 *  - Crypto payment option selection
 *  - Live exchange rate display (CoinGecko API)
 *  - Wallet address generation
 *  - Payment confirmation (manual/simulated)
 */

const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:8000';

async function loginAsCitizen(page) {
  await page.goto(`${BASE_URL}/login`);
  await page.fill('#email', 'citizen@test.com');
  await page.fill('#password', 'password');
  await page.click('#login-submit');
  await expect(page).toHaveURL(`${BASE_URL}/citizen/dashboard`);
}

// ─────────────────────────────────────────────
// TEST SUITE: Cryptocurrency Payment
// ─────────────────────────────────────────────
test.describe('Cryptocurrency Payment', () => {

  test('TC-CRYPTO-01: Crypto payment option is available on payment page', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/4`);

    await expect(page.locator('#payment-method-crypto')).toBeVisible();
    await page.click('#payment-method-crypto');
    await expect(page.locator('.crypto-payment-panel')).toBeVisible();
  });

  test('TC-CRYPTO-02: Live exchange rate is displayed from CoinGecko API', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/4`);
    await page.click('#payment-method-crypto');

    // Select BTC
    await page.selectOption('#crypto-currency', 'BTC');
    await page.waitForSelector('.crypto-rate', { timeout: 5000 });

    const rateText = await page.locator('.crypto-rate').textContent();
    expect(rateText).toMatch(/\d+(\.\d+)?\s*BTC/);
  });

  test('TC-CRYPTO-03: Switching crypto currency updates the amount', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/4`);
    await page.click('#payment-method-crypto');

    await page.selectOption('#crypto-currency', 'BTC');
    await page.waitForSelector('.crypto-amount');
    const btcAmount = await page.locator('.crypto-amount').textContent();

    await page.selectOption('#crypto-currency', 'ETH');
    await page.waitForSelector('.crypto-amount');
    const ethAmount = await page.locator('.crypto-amount').textContent();

    expect(btcAmount).not.toEqual(ethAmount);
  });

  test('TC-CRYPTO-04: Wallet address is generated and displayed', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/4`);
    await page.click('#payment-method-crypto');
    await page.selectOption('#crypto-currency', 'USDT');
    await page.click('#generate-wallet-btn');

    await expect(page.locator('.wallet-address')).toBeVisible();
    const address = await page.locator('.wallet-address').textContent();
    expect(address.trim().length).toBeGreaterThan(20); // Valid address length
  });

  test('TC-CRYPTO-05: Confirming payment marks request as Paid', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/4`);
    await page.click('#payment-method-crypto');
    await page.selectOption('#crypto-currency', 'USDT');
    await page.click('#generate-wallet-btn');

    // Simulate payment confirmation (test/demo mode)
    await page.click('#confirm-crypto-payment');

    await expect(page).toHaveURL(/\/payment\/success|citizen\/dashboard/);
    await expect(page.locator('.request-status, .payment-status')).toContainText(/paid|success/i);
  });

});


/**
 * E2E Test: QR Code Tracking Flow
 * Author: Person D
 * Sprint: 4
 * Epic: Citizen Experience & Transactions
 *
 * Covers:
 *  - QR code display on request confirmation
 *  - Public tracking page (no login required)
 *  - Correct data shown on tracking page
 *  - No private data exposed on public page
 */

test.describe('QR Code & Public Tracking', () => {

  test('TC-QR-01: QR code is visible on request confirmation page', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/citizen/requests/1`);

    await expect(page.locator('.qr-code-image')).toBeVisible();
    const qrSrc = await page.locator('.qr-code-image').getAttribute('src');
    expect(qrSrc).toBeTruthy();
  });

  test('TC-QR-02: Public tracking page loads without login', async ({ page }) => {
    // Use a fresh browser context — no cookies / session
    await page.goto(`${BASE_URL}/track/test-uuid-1234`);

    // Should NOT redirect to login
    await expect(page).not.toHaveURL(/\/login/);
    await expect(page.locator('.tracking-service-name')).toBeVisible();
    await expect(page.locator('.tracking-status')).toBeVisible();
  });

  test('TC-QR-03: Tracking page shows correct service and office name', async ({ page }) => {
    await page.goto(`${BASE_URL}/track/test-uuid-1234`);

    await expect(page.locator('.tracking-service-name')).toContainText('Birth Certificate');
    await expect(page.locator('.tracking-office-name')).toContainText('Tripoli Civil Registry');
  });

  test('TC-QR-04: Tracking page does NOT expose citizen private data', async ({ page }) => {
    await page.goto(`${BASE_URL}/track/test-uuid-1234`);

    const pageContent = await page.content();
    // Ensure no email addresses or ID numbers are visible
    expect(pageContent).not.toMatch(/citizen@test\.com/);
    expect(pageContent).not.toMatch(/national.id/i);
  });

  test('TC-QR-05: Invalid UUID shows 404 not found page', async ({ page }) => {
    await page.goto(`${BASE_URL}/track/invalid-uuid-0000`);
    await expect(page.locator('body')).toContainText(/404|not found/i);
  });

});

/**
 * E2E Test: Star Rating System
 * Author: Person D
 * Sprint: 4
 */

test.describe('Rating & Review System', () => {

  test('TC-RATE-01: Rating widget appears only on completed requests', async ({ page }) => {
    await loginAsCitizen(page);

    // Pending request — no rating widget
    await page.goto(`${BASE_URL}/citizen/requests/1`); // Pending
    await expect(page.locator('.rating-widget')).not.toBeVisible();

    // Completed request — rating widget visible
    await page.goto(`${BASE_URL}/citizen/requests/5`); // Completed
    await expect(page.locator('.rating-widget')).toBeVisible();
  });

  test('TC-RATE-02: Citizen can submit a 5-star rating with a comment', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/citizen/requests/5`);

    await page.click('.star-5'); // Click 5th star
    await page.fill('#rating-comment', 'Excellent service, very fast!');
    await page.click('#submit-rating');

    await expect(page.locator('.rating-success')).toContainText(/thank|submitted/i);
  });

  test('TC-RATE-03: Submitted rating appears on office public profile', async ({ page }) => {
    await page.goto(`${BASE_URL}/offices/1`); // Office public page

    await expect(page.locator('.reviews-section')).toBeVisible();
    await expect(page.locator('.review-comment').first()).toContainText('Excellent service');
    await expect(page.locator('.average-rating')).toBeVisible();
  });

  test('TC-RATE-04: Citizen cannot rate the same request twice', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/citizen/requests/5`);

    // Rating was already submitted — widget should be replaced with submitted message
    await expect(page.locator('.rating-already-submitted')).toBeVisible();
    await expect(page.locator('.rating-widget')).not.toBeVisible();
  });

});
