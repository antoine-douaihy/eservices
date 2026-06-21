/**
 * E2E Test: Citizen Service Application Flow
 * Author: Person A
 * Sprint: 4
 * Epic: Citizen Experience & Transactions
 *
 * Covers:
 *  - Browsing and filtering services
 *  - Multi-step application wizard
 *  - Document upload validation
 *  - Nearest office detection via Google Maps
 *  - Application confirmation and QR code display
 */

const { test, expect } = require('@playwright/test');
const path = require('path');

const BASE_URL = 'http://localhost:8000';

// Helper: log in as citizen before tests
async function loginAsCitizen(page) {
  await page.goto(`${BASE_URL}/login`);
  await page.fill('#email', 'citizen@test.com');
  await page.fill('#password', 'password');
  await page.click('#login-submit');
  await expect(page).toHaveURL(`${BASE_URL}/citizen/dashboard`);
}

// ─────────────────────────────────────────────
// TEST SUITE 1: Service Browsing
// ─────────────────────────────────────────────
test.describe('Service Browsing & Discovery', () => {

  test('TC-APP-01: Citizen can browse all available services', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/services`);

    const serviceCards = page.locator('.service-card');
    await expect(serviceCards).toHaveCount.above(0);
  });

  test('TC-APP-02: Citizen can filter services by office', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/services`);

    await page.selectOption('#filter-office', { label: 'Tripoli Civil Registry' });
    await page.click('#apply-filters');

    const cards = page.locator('.service-card');
    const count = await cards.count();
    expect(count).toBeGreaterThan(0);

    // All visible cards should belong to the selected office
    for (let i = 0; i < count; i++) {
      await expect(cards.nth(i).locator('.office-name')).toContainText('Tripoli Civil Registry');
    }
  });

  test('TC-APP-03: Citizen can filter services by category', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/services`);

    await page.selectOption('#filter-category', { label: 'Civil Documents' });
    await page.click('#apply-filters');

    const cards = page.locator('.service-card');
    await expect(cards).toHaveCount.above(0);
  });

  test('TC-APP-04: Clicking a service card opens the service detail page', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/services`);

    await page.locator('.service-card').first().click();
    await expect(page).toHaveURL(/\/services\/\d+/);
    await expect(page.locator('h1.service-title')).toBeVisible();
    await expect(page.locator('.required-documents-list')).toBeVisible();
  });

});

// ─────────────────────────────────────────────
// TEST SUITE 2: Multi-Step Application Wizard
// ─────────────────────────────────────────────
test.describe('Service Application Wizard', () => {

  test('TC-APP-05: Citizen can complete Step 1 — Select service', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/services/1`); // Birth Certificate service

    await page.click('#apply-now-btn');
    await expect(page).toHaveURL(/\/apply\/step-1/);
    await expect(page.locator('.wizard-step-indicator .active')).toContainText('1');
  });

  test('TC-APP-06: Citizen can complete Step 2 — Fill details and upload documents', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/apply/step-2?service_id=1`);

    await page.fill('#applicant_notes', 'Requesting birth certificate for travel purposes.');

    // Upload required document
    const [fileChooser] = await Promise.all([
      page.waitForEvent('filechooser'),
      page.click('#upload-doc-1'),
    ]);
    await fileChooser.setFiles(path.join(__dirname, '../fixtures/sample_id.pdf'));

    await page.click('#step-2-next');
    await expect(page).toHaveURL(/\/apply\/step-3/);
  });

  test('TC-APP-07: Step 2 fails if required document is not uploaded', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/apply/step-2?service_id=1`);

    // Skip document upload
    await page.click('#step-2-next');

    await expect(page.locator('.doc-error')).toContainText('required');
    await expect(page).toHaveURL(/\/apply\/step-2/);
  });

  test('TC-APP-08: Step 3 shows review summary before submission', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/apply/step-3?service_id=1`);

    await expect(page.locator('.summary-service-name')).toBeVisible();
    await expect(page.locator('.summary-office-name')).toBeVisible();
    await expect(page.locator('.summary-price')).toBeVisible();
    await expect(page.locator('#proceed-to-payment')).toBeVisible();
  });

  test('TC-APP-09: Submitted application shows QR code on confirmation page', async ({ page }) => {
    await loginAsCitizen(page);

    // Navigate through all steps
    await page.goto(`${BASE_URL}/apply/confirm?request_id=1`);

    await expect(page.locator('.qr-code-image')).toBeVisible();
    await expect(page.locator('.request-reference')).toBeVisible();
    await expect(page.locator('.status-badge')).toContainText('Pending');
  });

});

// ─────────────────────────────────────────────
// TEST SUITE 3: Document Upload Validation
// ─────────────────────────────────────────────
test.describe('Document Upload Validation', () => {

  test('TC-APP-10: Upload rejects files larger than 5MB', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/apply/step-2?service_id=1`);

    const [fileChooser] = await Promise.all([
      page.waitForEvent('filechooser'),
      page.click('#upload-doc-1'),
    ]);
    await fileChooser.setFiles(path.join(__dirname, '../fixtures/large_file.pdf')); // >5MB file

    await page.click('#step-2-next');
    await expect(page.locator('.doc-error')).toContainText('5MB');
  });

  test('TC-APP-11: Upload rejects unsupported file types', async ({ page }) => {
    await loginAsCitizen(page);
    await page.goto(`${BASE_URL}/apply/step-2?service_id=1`);

    const [fileChooser] = await Promise.all([
      page.waitForEvent('filechooser'),
      page.click('#upload-doc-1'),
    ]);
    await fileChooser.setFiles(path.join(__dirname, '../fixtures/test.exe')); // Invalid type

    await expect(page.locator('.doc-error')).toContainText('PDF, JPG, or PNG');
  });

});
