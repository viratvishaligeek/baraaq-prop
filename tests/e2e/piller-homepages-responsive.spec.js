import { test, expect } from '@playwright/test';

const homepages = [
  { name: 'Home 1', path: '/' },
  { name: 'Home 2', path: '/home-2.html' },
  { name: 'Home 3', path: '/home-3.html' },
  { name: 'Home 4', path: '/home-4.html' },
  { name: 'Home 5', path: '/home-5.html' },
  { name: 'Home 6', path: '/home-6.html' },
  { name: 'Home 7', path: '/home-7.html' },
  { name: 'Home 8', path: '/home-8.html' },
  { name: 'Home 9', path: '/home-9.html' },
  { name: 'Home 10', path: '/home-10.html' },
];

const viewports = [
  { name: 'desktop', width: 1440, height: 1000 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
];

for (const homepage of homepages) {
  test(`${homepage.name} renders at desktop, tablet and mobile widths`, async ({ page }) => {
    for (const viewport of viewports) {
      await page.setViewportSize({ width: viewport.width, height: viewport.height });
      const response = await page.goto(homepage.path, { waitUntil: 'domcontentloaded' });

      expect(response, `${homepage.name} should return a response on ${viewport.name}`).not.toBeNull();
      expect(response.status(), `${homepage.name} should return HTTP 200 on ${viewport.name}`).toBe(200);
      await expect(page.locator('body')).toBeVisible();

      const dimensions = await page.evaluate(() => ({
        viewport: document.documentElement.clientWidth,
        document: document.documentElement.scrollWidth,
      }));

      expect(
        dimensions.document,
        `${homepage.name} has horizontal overflow on ${viewport.name}: ${JSON.stringify(dimensions)}`,
      ).toBeLessThanOrEqual(dimensions.viewport + 2);
    }
  });
}
