import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/elgg';

/**
 * The plugin overrides `profile/details` to use hypePrototyper's profile view.
 * This test verifies the user profile page still renders for an authenticated
 * viewer and contains the expected user-identity markers.
 */
test.describe('prototyper_profile: profile view', () => {
  test('profile page renders prototyped details', async ({ page }) => {
    await loginAs(page, 'testuser');
    await page.goto('/profile/testuser');

    await expect(page.locator('#profile-details')).toBeVisible();
    await expect(page.locator('.p-name')).toBeVisible();

    // No system error messages
    await expect(
      page.locator('.elgg-system-messages .elgg-message-error')
    ).toHaveCount(0);
  });
});
