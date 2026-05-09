import { test, expect } from '@playwright/test';
import { loginAs, getUserByUsername, getMetadata } from '../helpers/elgg';

/**
 * End-to-end coverage for the prototyper-powered user profile edit form.
 *
 * These tests assume:
 *   - An admin user named `admin` (password `testpass123`) exists.
 *   - A regular user named `testuser` (password `testpass123`) exists.
 *   - The prototyper_profile plugin and its hypeprototyper dependency are active.
 */
test.describe('prototyper_profile: profile edit form', () => {
  test('profile edit page renders the prototyped form', async ({ page }) => {
    await loginAs(page, 'testuser');
    await page.goto('/profile/testuser/edit');

    // Form container should render
    await expect(page.locator('form')).toBeVisible();

    // No error messages
    await expect(
      page.locator('.elgg-system-messages .elgg-message-error')
    ).toHaveCount(0);
  });

  test('submitting the profile edit form updates user metadata', async ({ page }) => {
    await loginAs(page, 'testuser');
    await page.goto('/profile/testuser/edit');

    // The "briefdescription" metadata field is a default Elgg profile field
    // Fill it with a unique value and submit
    const unique = `bio ${Date.now()}`;
    const field = page.locator('[name="briefdescription"]').first();

    if (await field.count() === 0) {
      test.skip(true, 'briefdescription field not in prototype — configure an admin prototype first');
    }

    await field.fill(unique);
    await page.click('input[type="submit"], button[type="submit"]');

    // Assert UI: redirected to profile
    await expect(page).toHaveURL(/\/profile\/testuser/);

    // Assert DB: metadata persisted
    const user = await getUserByUsername('testuser');
    expect(user).toBeTruthy();
    const md = await getMetadata(user.guid, 'briefdescription');
    expect(md.some((row: any) => row.value === unique)).toBe(true);
  });
});
