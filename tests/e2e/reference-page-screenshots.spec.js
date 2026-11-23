const { test, expect } = require('@playwright/test');
const path = require('node:path');
const fs = require('node:fs');

const viewports = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
];

const pages = [
  { name: 'home', reference: 'home-9.html', route: '/' },
  { name: 'about', reference: 'about.html', route: '/about' },
  { name: 'properties', reference: 'property.html', route: '/properties' },
  { name: 'gallery', reference: 'gallery.html', route: '/gallery' },
  { name: 'services', reference: 'service.html', route: '/services' },
  { name: 'neighborhood-guide', reference: 'neighborhood-guide.html', route: '/neighborhood-guide' },
  { name: 'faq', reference: 'faq.html', route: '/faq' },
  { name: 'blog', reference: 'blog.html', route: '/blog' },
  { name: 'contact', reference: 'contact.html', route: '/contact' },
];

const detailPages = [
  { name: 'property-details', reference: 'property-details.html', source: '/properties', selector: '.popular-list-1 a[href*="/properties/"]' },
  { name: 'service-details', reference: 'service-details.html', source: '/services', selector: '.service-card a[href*="/services/"]' },
  { name: 'blog-details', reference: 'blog-details.html', source: '/blog', selector: '.blog-card a[href*="/blog/"]' },
];

async function saveScreenshot(page, testInfo, name) {
  const file = testInfo.outputPath(name + '.png');
  fs.mkdirSync(path.dirname(file), { recursive: true });
  const bytes = await page.screenshot({ path: file, fullPage: true, animations: 'disabled' });
  await testInfo.attach(name, { body: bytes, contentType: 'image/png' });
}

for (const viewport of viewports) {
  test('capture original and Laravel screenshots for all reference listing pages at ' + viewport.name, async ({ page }, testInfo) => {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });

    for (const item of pages) {
      const referenceResponse = await page.goto('/reference/' + item.reference, { waitUntil: 'networkidle' });
      expect(referenceResponse?.status(), item.name + ' original reference must load').toBe(200);
      await saveScreenshot(page, testInfo, item.name + '-reference-' + viewport.name);

      const liveResponse = await page.goto(item.route, { waitUntil: 'networkidle' });
      expect(liveResponse?.status(), item.name + ' Laravel page must load').toBe(200);
      if (item.name === 'home') {
        await expect(page.locator('.th-hero-wrapper.hero-9')).toBeVisible();
        await expect(page.locator('.header-layout9')).toBeAttached();
        await expect(page.locator('.footer-layout1')).toBeAttached();
      } else {
        await expect(page.locator('.header-top')).toBeAttached();
        await expect(page.locator('.footer-default')).toBeAttached();
      }
      const dimensions = await page.evaluate(() => ({
        viewport: document.documentElement.clientWidth,
        content: document.documentElement.scrollWidth,
      }));
      expect(dimensions.content, item.name + ' must not overflow at ' + viewport.name)
        .toBeLessThanOrEqual(dimensions.viewport);
      await saveScreenshot(page, testInfo, item.name + '-laravel-' + viewport.name);
    }
  });

  test('capture original and Laravel screenshots for detail pages with published content at ' + viewport.name, async ({ page }, testInfo) => {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });

    for (const item of detailPages) {
      const sourceResponse = await page.goto(item.source, { waitUntil: 'networkidle' });
      expect(sourceResponse?.status()).toBe(200);
      const detailLink = page.locator(item.selector).first();
      const href = await detailLink.getAttribute('href').catch(() => null);
      if (!href) {
        testInfo.annotations.push({ type: 'not-compared', description: item.name + ' has no published CMS record in this test database.' });
        continue;
      }

      const referenceResponse = await page.goto('/reference/' + item.reference, { waitUntil: 'networkidle' });
      expect(referenceResponse?.status()).toBe(200);
      await saveScreenshot(page, testInfo, item.name + '-reference-' + viewport.name);

      const liveResponse = await page.goto(href, { waitUntil: 'networkidle' });
      expect(liveResponse?.status()).toBe(200);
      await saveScreenshot(page, testInfo, item.name + '-laravel-' + viewport.name);
    }
  });

  test('capture the branded 404 reference and Laravel response at ' + viewport.name, async ({ page }, testInfo) => {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    const referenceResponse = await page.goto('/reference/error.html', { waitUntil: 'networkidle' });
    expect(referenceResponse?.status()).toBe(200);
    await saveScreenshot(page, testInfo, 'error-reference-' + viewport.name);

    const liveResponse = await page.goto('/error.html', { waitUntil: 'networkidle' });
    expect(liveResponse?.status()).toBe(404);
    await saveScreenshot(page, testInfo, 'error-laravel-' + viewport.name);
  });
}
