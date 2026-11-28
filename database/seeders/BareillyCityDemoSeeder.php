<?php

namespace Database\Seeders;

use App\Models\CircleRate;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateCalculatorField;
use App\Models\CircleRateCalculatorFieldOption;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateImportBatch;
use App\Models\CircleRateLocation;
use App\Models\CircleRateLocationLevel;
use App\Models\CircleRatePropertyCategory;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateRoadWidthRange;
use App\Models\CircleRateRule;
use App\Models\CircleRateSource;
use App\Models\CircleRateTehsil;
use App\Models\CircleRateUnit;
use App\Models\CircleRateUnitConversion;
use App\Models\CircleRateVersion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Creates a fully synthetic, calculator-eligible Bareilly City dataset.
 *
 * IMPORTANT: These are deliberately published/verified TEST records so the
 * existing public calculator can be exercised without the admin approval
 * workflow during development. They are not government rates and must never
 * be used as official valuation data or deployed to production.
 *
 * Run explicitly:
 *   php artisan db:seed --class=BareillyCityDemoSeeder
 */
class BareillyCityDemoSeeder extends Seeder
{
    private const DEMO_NOTE = 'DEMO TEST DATA - NOT OFFICIAL GOVERNMENT CIRCLE RATE';
    private const SOURCE_REFERENCE = 'DEMO-BAREILLY-CITY-CIRCLE-RATES';
    private const VERIFIER_EMAIL = 'demo-circle-rate-verifier@example.invalid';

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Bareilly synthetic circle-rate data is blocked in production.');
        }

        DB::transaction(function (): void {
            $actor = User::query()->firstOrCreate(
                ['email' => self::VERIFIER_EMAIL],
                [
                    'name' => 'DEMO Seeder Verification Actor (Synthetic)',
                    'password' => Hash::make(Str::random(64)),
                    'is_active' => true,
                ]
            );
            $this->assertDemo(
                $actor->name === 'DEMO Seeder Verification Actor (Synthetic)',
                'synthetic verification actor'
            );

            $district = CircleRateDistrict::query()->firstOrCreate(
                ['slug' => 'bareilly'],
                ['name' => 'Bareilly', 'state' => 'Uttar Pradesh', 'is_active' => true]
            );

            $tehsil = CircleRateTehsil::query()->firstOrCreate(
                ['district_id' => $district->id, 'slug' => 'bareilly'],
                ['name' => 'Bareilly', 'is_active' => true]
            );

            $urban = CircleRateAreaType::query()->firstOrCreate(
                ['slug' => 'urban'],
                [
                    'name' => 'Urban',
                    'description' => 'Urban area type used by the Bareilly city demo dataset.',
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );

            $localityLevel = CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => 'locality'],
                ['name' => 'Locality', 'description' => 'City locality level.', 'is_required' => false, 'is_active' => true, 'sort_order' => 1]
            );
            $mohallaLevel = CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => 'mohalla'],
                ['name' => 'Mohalla', 'description' => 'Urban mohalla level.', 'is_required' => false, 'is_active' => true, 'sort_order' => 2]
            );
            $wardLevel = CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => 'ward'],
                ['name' => 'Ward', 'description' => 'Urban ward level.', 'is_required' => false, 'is_active' => true, 'sort_order' => 3]
            );

            $locations = [];
            foreach ([
                'Civil Lines', 'Prem Nagar', 'Rajendra Nagar', 'Model Town',
                'DD Puram', 'Izzatnagar', 'Subhash Nagar', 'Shahamatganj',
                'Qila', 'Kotwali', 'Suresh Sharma Nagar', 'Green Park',
            ] as $name) {
                $slug = 'demo-'.Str::slug($name);
                $location = CircleRateLocation::query()->firstOrCreate(
                    ['district_id' => $district->id, 'parent_id' => null, 'slug' => $slug],
                    [
                        'tehsil_id' => $tehsil->id,
                        'area_type_id' => $urban->id,
                        'level_id' => $localityLevel->id,
                        'name' => $name.' (DEMO)',
                        'attributes' => ['dataset' => 'DEMO', 'notice' => self::DEMO_NOTE],
                        'is_active' => true,
                    ]
                );
                $this->assertDemo(str_contains((string) ($location->attributes['notice'] ?? ''), self::DEMO_NOTE), 'locality '.$name);
                $locations[$name] = $location;
            }

            foreach ([
                ['Civil Lines', 'Civil Lines Central Mohalla', $mohallaLevel, 'mohalla'],
                ['Prem Nagar', 'Prem Nagar Extension', $mohallaLevel, 'mohalla'],
                ['Rajendra Nagar', 'Rajendra Nagar Ward 1', $wardLevel, 'ward'],
                ['Model Town', 'Model Town Ward 2', $wardLevel, 'ward'],
            ] as [$parentName, $childName, $level, $kind]) {
                $parent = $locations[$parentName];
                $child = CircleRateLocation::query()->firstOrCreate(
                    ['district_id' => $district->id, 'parent_id' => $parent->id, 'slug' => 'demo-'.Str::slug($childName)],
                    [
                        'tehsil_id' => $tehsil->id,
                        'area_type_id' => $urban->id,
                        'level_id' => $level->id,
                        'name' => $childName.' (DEMO)',
                        'attributes' => ['dataset' => 'DEMO', 'location_kind' => $kind, 'notice' => self::DEMO_NOTE],
                        'is_active' => true,
                    ]
                );
                $this->assertDemo(str_contains((string) ($child->attributes['notice'] ?? ''), self::DEMO_NOTE), 'child location '.$childName);
            }

            $typeDefinitions = [
                'agricultural_land' => ['Agricultural Land (DEMO)', 'agricultural_land', 95],
                'residential_plot' => ['Residential Plot (DEMO)', 'residential_plot', 1250],
                'commercial_plot' => ['Commercial Plot (DEMO)', 'commercial_plot', 1850],
                'residential_building' => ['Residential Building (DEMO)', 'building_area', 1650],
                'commercial_building' => ['Commercial Building (DEMO)', 'building_area', 2450],
                'industrial_property' => ['Industrial Property (DEMO)', 'industrial_plot', 720],
                'mixed_use_property' => ['Mixed-Use Property (DEMO)', 'mixed_use_land', 1380],
            ];

            $types = [];
            foreach ($typeDefinitions as $slug => [$name, $strategy]) {
                $type = CircleRatePropertyType::query()->firstOrCreate(
                    ['slug' => 'demo-'.$slug],
                    [
                        'name' => $name,
                        'description' => self::DEMO_NOTE,
                        'calculation_strategy' => $strategy,
                        'field_configuration' => ['dataset' => 'DEMO'],
                        'formula_configuration' => null,
                        'is_active' => true,
                        'sort_order' => count($types) + 1,
                    ]
                );
                $this->assertDemo(str_contains((string) $type->description, self::DEMO_NOTE), 'property type '.$slug);
                $types[$slug] = $type;
            }

            // Categories are intentionally compatible with the requested demo
            // journeys, including Commercial Plot + Internal Road (Test B).
            $categoryDefinitions = [
                'main-road' => ['Main Road (DEMO)', ['residential_plot', 'commercial_plot', 'commercial_building', 'mixed_use_property']],
                'internal-road' => ['Internal Road (DEMO)', ['residential_plot', 'commercial_plot', 'residential_building', 'mixed_use_property']],
                'corner-property' => ['Corner Property (DEMO)', ['residential_plot', 'commercial_plot', 'residential_building', 'commercial_building', 'mixed_use_property']],
                'residential-colony' => ['Residential Colony (DEMO)', ['residential_plot', 'residential_building', 'mixed_use_property']],
                'commercial-market' => ['Commercial Market (DEMO)', ['commercial_plot', 'commercial_building', 'mixed_use_property']],
                'developed-area' => ['Developed Area (DEMO)', ['residential_plot', 'commercial_plot', 'residential_building', 'commercial_building', 'industrial_property', 'mixed_use_property']],
                'undeveloped-area' => ['Undeveloped Area (DEMO)', ['agricultural_land', 'residential_plot', 'industrial_property', 'mixed_use_property']],
                'agricultural-category' => ['Agricultural Category (DEMO)', ['agricultural_land']],
            ];

            $categories = [];
            foreach ($categoryDefinitions as $slug => [$name, $applicableTypes]) {
                $category = CircleRatePropertyCategory::query()->firstOrCreate(
                    ['slug' => 'demo-'.$slug],
                    [
                        'name' => $name,
                        'description' => self::DEMO_NOTE,
                        // Keep the district-wide catch-all below any more-specific
            // official/manual rate. Specific demo fixtures retain high priority.
            'priority' => ($tehsil === null && $location === null && $areaType === null && $category === null && $road === null) ? 0 : 100,
                        'applicability' => ['dataset' => 'DEMO', 'property_type_slugs' => $applicableTypes],
                        'is_active' => true,
                    ]
                );
                $this->assertDemo(str_contains((string) $category->description, self::DEMO_NOTE), 'category '.$slug);
                $category->propertyTypes()->syncWithoutDetaching(
                    collect($applicableTypes)->map(fn (string $typeSlug) => $types[$typeSlug]->id)->all()
                );
                $category->locations()->syncWithoutDetaching(collect($locations)->pluck('id')->all());
                $categories[$slug] = $category;
            }

            // All rate units are area units. The Bigha factor is synthetic and
            // explicitly configurable; it is not represented as a legal local factor.
            $unitDefinitions = [
                'square-foot' => ['Square Foot (DEMO)', 'sq ft', 1.0, true],
                'square-metre' => ['Square Metre (DEMO)', 'sq m', 10.7639, false],
                'acre' => ['Acre (DEMO)', 'acre', 43560.0, false],
                'hectare' => ['Hectare (DEMO)', 'ha', 107639.0, false],
                'bigha' => ['Bigha (CONFIGURABLE DEMO)', 'bigha', 27225.0, false],
            ];

            $units = [];
            foreach ($unitDefinitions as $slug => [$name, $symbol, $factor, $isBase]) {
                $unit = CircleRateUnit::query()->firstOrCreate(
                    ['slug' => 'demo-'.$slug],
                    [
                        'name' => $name,
                        'symbol' => $symbol,
                        'dimension' => 'area',
                        'base_factor' => $factor,
                        'is_base_unit' => $isBase,
                        'is_active' => true,
                    ]
                );
                $this->assertDemo(str_contains((string) $unit->name, 'DEMO'), 'unit '.$slug);
                $units[$slug] = $unit;
            }

            $metre = CircleRateUnit::query()->firstOrCreate(
                ['slug' => 'demo-metre'],
                [
                    'name' => 'Metre (DEMO road-width unit)',
                    'symbol' => 'm',
                    'dimension' => 'length',
                    'base_factor' => 1,
                    'is_base_unit' => true,
                    'is_active' => true,
                ]
            );

            // Every directed conversion is verified sample configuration so
            // every visible area unit can calculate against every demo rate unit.
            foreach ($units as $from) {
                foreach ($units as $to) {
                    if ($from->id === $to->id) {
                        continue;
                    }
                    $conversion = CircleRateUnitConversion::query()->firstOrCreate(
                        ['from_unit_id' => $from->id, 'to_unit_id' => $to->id],
                        [
                            'factor' => (float) $from->base_factor / (float) $to->base_factor,
                            'source_note' => self::DEMO_NOTE.'; synthetic conversion factor for calculator testing only.',
                            'is_verified' => true,
                        ]
                    );
                    if (str_contains((string) $conversion->source_note, self::DEMO_NOTE)) {
                        $conversion->forceFill([
                            'factor' => (float) $from->base_factor / (float) $to->base_factor,
                            'source_note' => self::DEMO_NOTE.'; synthetic conversion factor for calculator testing only.',
                            'is_verified' => true,
                        ])->save();
                    }
                }
            }

            $roadWidths = [];
            foreach ([
                ['Below 6 metres (DEMO)', 0, 5.9999, 1],
                ['6–9 metres (DEMO)', 6, 8.9999, 2],
                ['9–12 metres (DEMO)', 9, 11.9999, 3],
                ['12–18 metres (DEMO)', 12, 18, 4],
                ['Above 18 metres (DEMO)', 18.0001, null, 5],
            ] as [$name, $minimum, $maximum, $priority]) {
                $road = CircleRateRoadWidthRange::query()->firstOrCreate(
                    ['name' => $name],
                    [
                        'unit_id' => $metre->id,
                        'minimum_width' => $minimum,
                        'maximum_width' => $maximum,
                        'applicability' => ['dataset' => 'DEMO'],
                        'adjustment_configuration' => ['dataset' => 'DEMO', 'rate_multiplier' => 1],
                        'effective_from' => null,
                        'effective_until' => null,
                        'priority' => $priority,
                        'is_active' => true,
                    ]
                );
                $roadWidths[$priority] = $road;
            }

            $years = [];
            foreach ([
                ['2025-26', '2025-04-01', '2026-03-31'],
                ['2026-27', '2026-04-01', '2027-03-31'],
            ] as [$name, $starts, $ends]) {
                $year = CircleRateFinancialYear::query()->firstOrCreate(
                    ['name' => $name],
                    [
                        'starts_on' => $starts,
                        'ends_on' => $ends,
                        'is_current' => $name === '2026-27',
                        'is_active' => true,
                        'show_on_frontend' => true,
                    ]
                );
                $this->assertDemo(
                    $year->starts_on->toDateString() === $starts
                    && $year->ends_on->toDateString() === $ends
                    && $year->is_active
                    && $year->show_on_frontend,
                    'financial year '.$name
                );
                $years[$name] = $year;
            }

            // Create a real local synthetic source document, not a fabricated
            // government URL. The source and file both identify the dataset as DEMO.
            $documentPath = 'circle-rate-demo/bareilly/'.Str::slug(self::SOURCE_REFERENCE).'.txt';
            $documentContents = implode("\n", [
                self::DEMO_NOTE,
                'Dataset: Bareilly City calculator test fixture',
                'Generated by: BareillyCityDemoSeeder',
                'Purpose: development and QA only',
                'This document is synthetic and is not a government notification, rate schedule, approval, or legal evidence.',
                'All rates, conversions, and calculation rules in this dataset are illustrative test values.',
            ]);
            Storage::disk('public')->put($documentPath, $documentContents);

            $source = CircleRateSource::query()->firstOrCreate(
                ['reference_number' => self::SOURCE_REFERENCE],
                [
                    'title' => self::DEMO_NOTE,
                    'document_path' => $documentPath,
                    'document_disk' => 'public',
                    'source_url' => null,
                    'published_on' => null,
                    'notes' => self::DEMO_NOTE.'; synthetic source document generated locally for non-production calculator testing.',
                    'uploaded_by' => $actor->id,
                ]
            );
            $this->assertDemo(
                $source->title === self::DEMO_NOTE
                && str_contains((string) $source->notes, self::DEMO_NOTE),
                'demo source'
            );
            if ($source->document_path !== $documentPath || $source->document_disk !== 'public') {
                $source->forceFill(['document_path' => $documentPath, 'document_disk' => 'public'])->save();
            }

            // Retire only the old pending/draft demo rates created by V1 of this
            // same seeder. Keep their version history; never touch manual/imported rates.
            CircleRate::query()
                ->where('source_id', $source->id)
                ->where('admin_notes', 'like', '%'.self::DEMO_NOTE.'%')
                ->where(function ($query): void {
                    $query->where('status', '!=', 'published')
                        ->orWhere('verification_status', '!=', 'verified');
                })
                ->get()
                ->each(fn (CircleRate $legacyRate) => $legacyRate->delete());

            $baseRates = collect($typeDefinitions)->mapWithKeys(
                fn (array $definition, string $slug) => [$slug => (float) $definition[2]]
            )->all();

            $rateCount = 0;
            foreach ($types as $typeSlug => $type) {
                $applicableCategories = collect($categoryDefinitions)
                    ->filter(fn (array $definition) => in_array($typeSlug, $definition[1], true))
                    ->keys()
                    ->map(fn (string $slug) => $categories[$slug])
                    ->values();

                // District-wide DEMO fallback: this row intentionally has no
                // tehsil, location, area type, category or road-width restriction.
                // The calculator resolver treats null optional dimensions as a
                // wildcard, so every valid Bareilly form combination can still
                // calculate when no more-specific sample rate exists.
                foreach (array_values($years) as $yearIndex => $year) {
                    $this->seedRate(
                        $district, null, null, null, $type, null, null,
                        $year, $units['square-foot'],
                        $source, $actor, $baseRates[$typeSlug], 0, $yearIndex
                    );
                    $rateCount++;
                }

                foreach (array_values($locations) as $locationIndex => $location) {
                    foreach (array_values($years) as $yearIndex => $year) {
                        // General fallback makes the type/location/year calculable
                        // even when category and road-width are not selected.
                        $this->seedRate(
                            $district, $tehsil, $urban, $location, $type, null, null,
                            $year, $units['square-foot'],
                            $source, $actor, $baseRates[$typeSlug], $locationIndex, $yearIndex
                        );
                        $rateCount++;

                        foreach ($applicableCategories as $categoryIndex => $category) {
                            foreach ($roadWidths as $roadIndex => $road) {
                                $unitValues = array_values($units);
                                $unit = $unitValues[($locationIndex + $categoryIndex + $roadIndex + $yearIndex) % count($unitValues)];
                                $this->seedRate(
                                    $district, $tehsil, $urban, $location, $type, $category, $road,
                                    $year, $unit, $source, $actor, $baseRates[$typeSlug],
                                    $locationIndex + $categoryIndex + $roadIndex, $yearIndex
                                );
                                $rateCount++;
                            }
                        }
                    }
                }
            }

            // Existing active property types outside this demo fixture also get
            // a generic district-wide test rate, so the public selector never
            // exposes a type that cannot be calculated in the Bareilly QA dataset.
            $demoTypeIds = collect($types)->pluck('id')->map(fn ($id) => (int) $id)->all();
            foreach (CircleRatePropertyType::query()->where('is_active', true)->get() as $otherType) {
                if (in_array((int) $otherType->id, $demoTypeIds, true)) {
                    continue;
                }

                foreach (array_values($years) as $yearIndex => $year) {
                    $this->seedRate(
                        $district, null, null, null, $otherType, null, null,
                        $year, $units['square-foot'],
                        $source, $actor, 500.0, 0, $yearIndex
                    );
                    $rateCount++;
                }
            }

            $this->seedCalculatorFields($types);
            $this->seedCalculationRules($source, $actor, $types, $categories, $roadWidths);

            $hash = hash('sha256', 'BAREILLY-CITY-DEMO-TEST-DATA-V2');
            CircleRateImportBatch::query()->firstOrCreate(
                ['file_hash' => $hash],
                [
                    'financial_year_id' => $years['2026-27']->id,
                    'source_id' => $source->id,
                    'uploaded_by' => $actor->id,
                    'original_filename' => 'DEMO-BAREILLY-CITY-TEST-DATA.csv',
                    'stored_path' => null,
                    'file_type' => 'csv',
                    'status' => 'uploaded',
                    'column_mapping' => ['dataset' => 'DEMO'],
                    'total_rows' => 0,
                    'valid_rows' => 0,
                    'invalid_rows' => 0,
                    'conflict_rows' => 0,
                    'summary' => self::DEMO_NOTE.'; metadata-only import fixture; no official import was performed.',
                ]
            );
        });
    }

    private function seedRate(
        CircleRateDistrict $district,
        ?CircleRateTehsil $tehsil,
        ?CircleRateAreaType $areaType,
        ?CircleRateLocation $location,
        CircleRatePropertyType $type,
        ?CircleRatePropertyCategory $category,
        ?CircleRateRoadWidthRange $road,
        CircleRateFinancialYear $year,
        CircleRateUnit $unit,
        CircleRateSource $source,
        User $actor,
        float $baseRate,
        int $sequence,
        int $yearIndex
    ): void {
        $identity = [
            'district_id' => $district->id,
            'tehsil_id' => $tehsil->id,
            'location_id' => $location->id,
            'area_type_id' => $areaType->id,
            'property_type_id' => $type->id,
            'category_id' => $category?->id,
            'road_width_range_id' => $road?->id,
            'financial_year_id' => $year->id,
            'unit_id' => $unit->id,
            'source_id' => $source->id,
            'effective_from' => $year->starts_on->toDateString(),
        ];

        $manualMatch = CircleRate::query()
            ->with('source')
            ->where('status', 'published')
            ->where('verification_status', 'verified')
            ->whereHas('source', fn ($query) => $query->whereNotNull('document_path')->orWhereNotNull('source_url'))
            ->where('district_id', $district->id)
            ->where('tehsil_id', $tehsil?->id)
            ->where('location_id', $location?->id)
            ->where('area_type_id', $areaType?->id)
            ->where('property_type_id', $type->id)
            ->where('category_id', $category?->id)
            ->where('road_width_range_id', $road?->id)
            ->where('financial_year_id', $year->id)
            ->whereDate('effective_from', '<=', $year->ends_on->toDateString())
            ->where(function ($query) use ($year): void {
                $query->whereNull('effective_until')
                    ->orWhereDate('effective_until', '>=', $year->starts_on->toDateString());
            })
            ->first();

        if ($manualMatch && (
            (int) $manualMatch->source_id !== (int) $source->id
            || ! str_contains((string) $manualMatch->admin_notes, self::DEMO_NOTE)
        )) {
            return; // Preserve manually entered/imported data without modification.
        }

        $rateAmount = round($baseRate * (1 + ($yearIndex * 0.075)) * (1 + (($sequence % 7) * 0.0125)), 4);
        $rate = CircleRate::query()->firstOrNew($identity);
        if ($rate->exists && ! str_contains((string) $rate->admin_notes, self::DEMO_NOTE)) {
            return;
        }

        $rate->forceFill([
            'rate' => $rateAmount,
            'priority' => 100,
            'effective_until' => $year->ends_on->toDateString(),
            'verification_status' => 'verified',
            'status' => 'published',
            'conditions' => [],
            'admin_notes' => self::DEMO_NOTE.'; synthetic test rate; published and verified by the dedicated DEMO seeder actor for non-production QA only.',
            'created_by' => $actor->id,
            'verified_by' => $actor->id,
            'verified_at' => now(),
        ])->save();

        CircleRateVersion::query()->updateOrCreate(
            ['circle_rate_id' => $rate->id, 'version_number' => 1],
            [
                'snapshot' => $rate->fresh()->getAttributes(),
                'change_reason' => self::DEMO_NOTE.'; initial synthetic calculator-ready sample version.',
                'changed_by' => $actor->id,
            ]
        );
    }

    private function seedCalculatorFields(array $types): void
    {
        $definitions = [
            ['land_classification', 'Land Classification', 'dropdown', ['Agricultural', 'Residential', 'Commercial'], ['agricultural_land']],
            ['plot_facing', 'Plot Facing', 'radio', ['North', 'South', 'East', 'West'], ['residential_plot', 'commercial_plot']],
            ['corner_plot', 'Corner Plot', 'checkbox', [], ['residential_plot', 'commercial_plot', 'mixed_use_property']],
            ['land_use', 'Land Use', 'select', ['Residential', 'Commercial', 'Mixed'], ['residential_plot', 'commercial_plot', 'mixed_use_property']],
            ['construction_type', 'Construction Type', 'dropdown', ['RCC', 'Load Bearing', 'Temporary'], ['residential_building', 'commercial_building']],
            ['building_age', 'Building Age', 'number', [], ['residential_building', 'commercial_building']],
            ['number_of_floors', 'Number of Floors', 'number', [], ['residential_building', 'commercial_building']],
            ['road_width', 'Road Width (metres)', 'number', [], null],
            ['development_status', 'Property Development Status', 'radio', ['Developed', 'Undeveloped'], null],
            ['site_notes', 'Site Notes', 'text', [], null],
            ['possession_date', 'Possession / Reference Date', 'date', [], null],
            ['additional_amenities', 'Additional Amenities', 'multi-select', ['Parking', 'Water', 'Electricity'], ['residential_building', 'commercial_building']],
        ];

        foreach ($definitions as $index => [$key, $label, $inputType, $options, $applicableSlugs]) {
            $ids = $applicableSlugs === null
                ? []
                : collect($applicableSlugs)->map(fn (string $slug) => $types[$slug]->id)->all();

            $field = CircleRateCalculatorField::query()->firstOrCreate(
                ['name' => 'demo_'.$key],
                [
                    'label' => $label.' (DEMO)',
                    'input_type' => $inputType,
                    'data_source' => null,
                    'placeholder' => 'Enter '.$label,
                    'help_text' => self::DEMO_NOTE,
                    'validation_rules' => match ($inputType) {
                        'number' => ['min' => 0],
                        'text' => ['max' => 500],
                        default => [],
                    },
                    'visibility_conditions' => $ids ? [[
                        'field' => 'property_type_id',
                        'operator' => 'in',
                        'value' => $ids,
                    ]] : [],
                    'applicability' => $ids ? ['property_type_ids' => $ids] : [],
                    'default_value' => null,
                    'is_required' => false,
                    'is_active' => true,
                    'sort_order' => 900 + $index,
                ]
            );

            $this->assertDemo(str_contains((string) $field->help_text, self::DEMO_NOTE), 'calculator field '.$key);

            foreach ($options as $optionIndex => $option) {
                CircleRateCalculatorFieldOption::query()->firstOrCreate(
                    ['field_id' => $field->id, 'value' => Str::slug($option, '_')],
                    ['label' => $option, 'sort_order' => $optionIndex + 1, 'is_active' => true]
                );
            }
        }
    }

    private function seedCalculationRules(
        CircleRateSource $source,
        User $actor,
        array $types,
        array $categories,
        array $roadWidths
    ): void {
        $definitions = [
            ['agricultural-valuation', 'Agricultural sample multiplier', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['agricultural_land']->id]], ['rate_multiplier' => 1.02], 100],
            ['residential-valuation', 'Residential sample multiplier', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['residential_plot']->id, $types['residential_building']->id]]], ['rate_multiplier' => 1.01], 90],
            ['commercial-valuation', 'Commercial sample multiplier', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['commercial_plot']->id, $types['commercial_building']->id]]], ['rate_multiplier' => 1.03], 80],
            ['industrial-valuation', 'Industrial sample multiplier', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['industrial_property']->id]], ['rate_multiplier' => 1.015], 70],
            ['mixed-use-valuation', 'Mixed-use sample multiplier', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['mixed_use_property']->id]], ['rate_multiplier' => 1.025], 60],
            ['road-width-condition', 'Road-width sample condition', [['field' => 'road_width_range_id', 'operator' => 'in', 'value' => collect($roadWidths)->pluck('id')->all()]], ['rate_multiplier' => 1.0], 50],
            ['category-condition', 'Category sample condition', [['field' => 'category_id', 'operator' => 'in', 'value' => collect($categories)->pluck('id')->all()]], ['rate_multiplier' => 1.0], 40],
            ['corner-plot-condition', 'Corner plot sample condition', [['field' => 'custom.demo_corner_plot', 'operator' => 'equals', 'value' => true]], ['rate_multiplier' => 1.025], 30],
            // Keep the historical rule identity, but do not require custom input:
            // the calculator scenarios must remain valid with optional fields omitted.
            ['require-building-details', 'Building details optional sample rule', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['residential_building']->id, $types['commercial_building']->id]]], ['rate_multiplier' => 1.0], 20],
        ];

        foreach ($definitions as [$slug, $description, $conditions, $actions, $priority]) {
            $name = 'DEMO – Bareilly City – '.$slug;
            $rule = CircleRateRule::query()->firstOrNew(['name' => $name]);
            if ($rule->exists && ! str_contains((string) $rule->description, self::DEMO_NOTE)) {
                continue;
            }

            $rule->forceFill([
                'description' => self::DEMO_NOTE.'; '.$description,
                'conditions' => $conditions,
                'actions' => $actions,
                'source_id' => $source->id,
                'is_verified' => true,
                'verified_by' => $actor->id,
                'verified_at' => now(),
                'priority' => $priority,
                'stop_processing' => false,
                'is_active' => true,
                'effective_from' => '2025-04-01',
                'effective_until' => '2027-03-31',
                'created_by' => $actor->id,
            ])->save();
        }
    }

    private function assertDemo(bool $valid, string $record): void
    {
        if (! $valid) {
            throw new RuntimeException('Refusing to reuse a non-demo record for '.$record.'. No official or manually managed record was changed.');
        }
    }
}
