/**
 * E2E Test: Fiat Payment Flow (Stripe)
 * Author: Person B
 * Sprint: 4
 * Epic: Citizen Experience & Transactions
 *
 * Covers:
 *  - Stripe card payment (sandbox)
 *  - Successful payment → request status update
 *  - Declined card handling
 *  - Payment confirmation page
 */

const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:8000';

// Stripe test card numbers
const STRIPE_SUCCESS_CARD = '4242 4242 4242 4242';
const STRIPE_DECLINED_CARD = '4000 0000 0000 0002';
const STRIPE_INSUFFICIENT_CARD = '4000 0000 0000 9995';

async function loginAsCitizen(page) {
  await page.goto(`${BASE_URL}/login`);
  await page.fill('#email', 'citizen@test.com');
  await page.fill('#password', 'password');
  await page.click('#login-submit');
  await expect(page).toHaveURL(`${BASE_URL}/citizen/dashboard`);
}

// ─────────────────────────────────────────────
// TEST SUITE 1: Stripe Card Payment
// ─────────────────────────────────────────────
test.describe('Fiat Payment — Stripe', () => {

  test('TC-PAY-01: Successful card payment updates request to Paid', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/1`); // Request ID 1

    // Fill Stripe Elements iframe
    const stripeFrame = page.frameLocator('iframe[name^="__privateStripeFrame"]').first();
    await stripeFrame.locator('[placeholder="Card number"]').fill(STRIPE_SUCCESS_CARD);
    await stripeFrame.locator('[placeholder="MM / YY"]').fill('12 / 28');
    await stripeFrame.locator('[placeholder="CVC"]').fill('123');
    await stripeFrame.locator('[placeholder="ZIP"]').fill('00000');

    await page.click('#pay-now-btn');

    // Wait for redirect to confirmation
    await expect(page).toHaveURL(/\/payment\/success/);
    await expect(page.locator('.payment-status')).toContainText('Payment Successful');
    await expect(page.locator('.request-status')).toContainText('Paid');
  });

  test('TC-PAY-02: Declined card shows error message', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/2`);

    const stripeFrame = page.frameLocator('iframe[name^="__privateStripeFrame"]').first();
    await stripeFrame.locator('[placeholder="Card number"]').fill(STRIPE_DECLINED_CARD);
    await stripeFrame.locator('[placeholder="MM / YY"]').fill('12 / 28');
    await stripeFrame.locator('[placeholder="CVC"]').fill('123');
    await stripeFrame.locator('[placeholder="ZIP"]').fill('00000');

    await page.click('#pay-now-btn');

    await expect(page.locator('.payment-error')).toContainText('declined');
    await expect(page).toHaveURL(/\/payment\/\d+/); // Stays on payment page
  });

  test('TC-PAY-03: Insufficient funds card shows specific error', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/3`);

    const stripeFrame = page.frameLocator('iframe[name^="__privateStripeFrame"]').first();
    await stripeFrame.locator('[placeholder="Card number"]').fill(STRIPE_INSUFFICIENT_CARD);
    await stripeFrame.locator('[placeholder="MM / YY"]').fill('12 / 28');
    await stripeFrame.locator('[placeholder="CVC"]').fill('123');
    await stripeFrame.locator('[placeholder="ZIP"]').fill('00000');

    await page.click('#pay-now-btn');

    await expect(page.locator('.payment-error')).toContainText(/insufficient|funds/i);
  });

  test('TC-PAY-04: Payment page shows correct service price', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/payment/1`);

    await expect(page.locator('.payment-amount')).toBeVisible();
    const priceText = await page.locator('.payment-amount').textContent();
    expect(priceText).toMatch(/\$\d+(\.\d{2})?/);
  });

});
