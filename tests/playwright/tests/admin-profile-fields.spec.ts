import { test, expect } from '@playwright/test';
import { loginAs, getPluginSetting } from '../helpers/elgg';

/**
 * Admin UI for configuring the profile prototype.
 *
 * The plugin exposes /admin/appearance/profile_fields which renders a
 * prototyper-powered form for the `profile/prototype` action.
 */
test.describe('prototyper_profile: admin profile_fields page', () => {
  test('admin can load profile_fields admin page', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/admin/appearance/profile_fields');

    expect(response?.status() ?? 0).toBeLessThan(400);
    await expect(
      page.locator('.elgg-system-messages .elgg-message-error')
    ).toHaveCount(0);

    // Form for the prototype action should render
    await expect(page.locator('form')).toBeVisible();
  });

  test('non-admin cannot access profile_fields admin page', async ({ page }) => {
    await loginAs(page, 'testuser');
    const response = await page.goto('/admin/appearance/profile_fields');

    // Elgg redirects non-admins away from /admin/*
    const status = response?.status() ?? 0;
    expect([200, 302, 403]).toContain(status);
    await expect(page).not.toHaveURL(/\/admin\/appearance\/profile_fields(\?|$)/);
  });

  test('non-admin POST to profile/prototype action is rejected', async ({ page, request }) => {
    await loginAs(page, 'testuser');

    // Grab CSRF tokens from a rendered page
    await page.goto('/');
    const tokens = await page.evaluate(() => {
      // @ts-ignore — elgg global
      const t = (window as any).elgg?.security?.token;
      return t ? { __elgg_token: t.__elgg_token, __elgg_ts: t.__elgg_ts } : null;
    });

    if (!tokens) {
      test.skip(true, 'Could not read Elgg CSRF tokens from window');
    }

    const response = await request.post('/action/profile/prototype', {
      form: {
        role: 'default',
        ...tokens!,
      },
    });

    // Access-restricted action — non-admin should not succeed (non-2xx or redirect).
    expect([302, 401, 403, 404]).toContain(response.status());
  });

  test.skip('admin saving a prototype writes prototype:<role> plugin setting', async ({ page }) => {
    // Requires building a full prototyper form payload, which depends on the
    // hypePrototyper UI. Deferred — covered indirectly by PluginSettingsTest.
    await loginAs(page, 'admin');
    await page.goto('/admin/appearance/profile_fields');
    // ...fill and submit the prototyper form here...
    const setting = await getPluginSetting('prototyper_profile', 'prototype:default');
    expect(setting).toBeTruthy();
  });
});
