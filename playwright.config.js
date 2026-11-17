import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  reporter: 'list',
  timeout: 30000,
  expect: { timeout: 5000 },
  use: {
    baseURL: process.env.APP_URL || 'http://127.0.0.1:8000',
    headless: true,
    trace: 'retain-on-failure',
  },
});
