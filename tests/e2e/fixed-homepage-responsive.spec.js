const { test, expect } = require('@playwright/test');

const viewports = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
];

for (const viewport of viewports) {
  test('fixed Home 9 renders at ' + viewport.name + ' size', async ({ page }) => {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });

    const response = await page.goto('/');
    expect(response?.status()).toBe(200);

    await expect(page.locator('.th-hero-wrapper.hero-9')).toBeVisible();
    await expect(page.locator('.th-hero-wrapper.hero-9 h1')).toContainText('Discover');
    await expect(page.locator('body')).toContainText('Most Suitable');
    await expect(page.locator('.footer-wrapper.footer-layout1')).toBeAttached();

    const documentWidth = await page.evaluate(() => ({
      viewport: document.documentElement.clientWidth,
      content: document.documentElement.scrollWidth,
    }));
    expect(
      documentWidth.content,
      'Home 9 must not introduce horizontal page overflow at ' + viewport.name + ' width.'
    ).toBeLessThanOrEqual(documentWidth.viewport);
  });
}

test('alternate homepage templates are unavailable and the legacy Home 9 URL redirects to root', async ({ page }) => {
  const legacyHomeNine = await page.goto('/home-9.html');
  expect(legacyHomeNine?.status()).toBe(200);
  expect(new URL(page.url()).pathname).toBe('/');

  const alternateHomepage = await page.goto('/home-2.html');
  expect(alternateHomepage?.status()).toBe(404);
});
