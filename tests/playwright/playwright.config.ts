import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  baseURL: process.env.ELGG_BASE_URL || 'http://elgg',
  timeout: 30000,
  use: {
    ignoreHTTPSErrors: true,
    trace: 'retain-on-failure',
  },
  // Sequential — tests share Elgg DB state
  workers: 1,
  projects: [
    { name: 'chromium', use: { browserName: 'chromium' } },
  ],
});
