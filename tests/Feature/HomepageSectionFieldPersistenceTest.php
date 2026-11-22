<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageSectionFieldPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_every_non_image_field_in_all_ten_homepage_sections_persists_by_its_declared_type(): void
    {
        $admin = $this->administrator();
        $homepage = Homepage::create([
            'name' => 'Home 9',
            'slug' => 'home-9',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $checkedFields = 0;

        foreach (config('cms-sections.sections', []) as $key => $definition) {
            $section = $homepage->sections()->create([
                'section_key' => $key,
                'section_type' => $definition['type'],
                'sort_order' => $checkedFields + 1,
                'is_enabled' => true,
                'config' => $definition['defaults'] ?? [],
                'data_source' => [],
            ]);

            $submitted = [];
            $expected = [];

            foreach ($definition['fields'] ?? [] as $fieldKey => $field) {
                $type = $field['type'] ?? 'text';

                if ($type === 'image') {
                    continue;
                }

                $value = match ($type) {
                    'checkbox' => false,
                    'number' => (string) max(1, (int) ($field['min'] ?? 1)),
                    'select' => (string) array_key_first($field['options'] ?? ['default' => 'Default']),
                    'url' => $fieldKey === 'video_url' ? 'https://example.com/video' : '/contact',
                    'color' => '#123456',
                    'richtext' => '<p>Persisted '.$key.' '.$fieldKey.'</p>',
                    default => 'Persisted '.$key.' '.$fieldKey,
                };

                $submitted[$fieldKey] = $value;
                $expected[$fieldKey] = $value;
                $checkedFields++;
            }

            $this->actingAs($admin)
                ->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
                    'config' => $submitted,
                ])
                ->assertRedirect(route('admin.homepages.index'));

            $saved = $section->fresh()->config;

            foreach ($expected as $fieldKey => $value) {
                if (($definition['fields'][$fieldKey]['type'] ?? 'text') === 'richtext') {
                    $this->assertStringContainsString(
                        strip_tags($value),
                        strip_tags((string) ($saved[$fieldKey] ?? '')),
                        "Rich-text field [{$key}.{$fieldKey}] did not persist."
                    );
                    continue;
                }

                $this->assertSame(
                    $value,
                    $saved[$fieldKey] ?? null,
                    "Field [{$key}.{$fieldKey}] did not persist its submitted value."
                );
            }
        }

        $this->assertSame(10, count(config('cms-sections.sections', [])), 'The fixed homepage must register exactly ten sections.');
        $this->assertGreaterThan(0, $checkedFields, 'Every section must expose editable non-image fields.');
    }
}
