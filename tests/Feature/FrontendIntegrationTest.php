<?php

namespace Tests\\Feature;

use App\\Models\\Enquiry;
use App\\Models\\Page;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class FrontendIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_uses_published_cms_content_and_stores_enquiries(): void
    {
        Page::query()->create([
            'title' => 'Contact Buraaqh Properties',
            'slug' => 'contact',
            'content' => '<p>Contact page copy managed from the CMS.</p>',
            'status' => 'published',
            'meta_title' => 'Contact Buraaqh Properties',
            'meta_description' => 'Send a property enquiry.',
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Contact page copy managed from the CMS.')
            ->assertSee('Contact Buraaqh Properties');

        $this->post(route('enquiries.store'), [
            'name' => 'Test Enquirer',
            'email' => 'enquirer@example.test',
            'subject' => 'Property enquiry',
            'message' => 'Please contact me about current property information.',
        ])->assertRedirect();

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Test Enquirer',
            'email' => 'enquirer@example.test',
            'source' => 'contact',
            'status' => 'new',
        ]);
        $this->assertSame(1, Enquiry::query()->count());
    }

    public function test_contact_enquiry_requires_at_least_one_contact_method(): void
    {
        $this->from(route('contact'))
            ->post(route('enquiries.store'), [
                'name' => 'Test Enquirer',
                'subject' => 'Property enquiry',
                'message' => 'Please contact me.',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['email', 'phone']);

        $this->assertSame(0, Enquiry::query()->count());
    }
}
