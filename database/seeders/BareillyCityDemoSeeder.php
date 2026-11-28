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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Temporary Bareilly city-only test data for the existing Circle Rate calculator.
 *
 * All sample rates remain DRAFT + PENDING and are explicitly labelled in their
 * source and admin notes. The existing calculator requires published, verified
 * records, so this seeder deliberately does not bypass that workflow or make
 * synthetic values eligible for official/public valuation.
 *
 * Run explicitly:
 *   php artisan db:seed --class=BareillyCityDemoSeeder
 */
class BareillyCityDemoSeeder extends Seeder
{
    private const DEMO_NOTE = 'DEMO TEST DATA - NOT OFFICIAL GOVERNMENT CIRCLE RATE';

    public function run(): void
    {
        DB::transaction(function (): void {
            $district = CircleRateDistrict::query()->firstOrCreate(
                ['slug' => 'bareilly'],
                ['name' => 'Bareilly', 'state' => 'Uttar Pradesh', 'is_active' => true]
            );

            // Bareilly urban tehsil only. No rural village/block directory is seeded.
            $tehsil = CircleRateTehsil::query()->firstOrCreate(
                ['district_id' => $district->id, 'slug' => 'bareilly'],
                ['name' => 'Bareilly', 'is_active' => true]
            );

            $urban = CircleRateAreaType::query()->firstOrCreate(
                ['slug' => 'urban'],
                ['name' => 'Urban', 'description' => 'Urban area type used by the Bareilly city demo dataset.', 'is_active' => true, 'sort_order' => 1]
            );

            $localityLevel = CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => 'locality'],
                ['name' => 'Locality', 'description' => 'City locality level.', 'is_required' => false, 'is_active' => true, 'sort_order' => 1]
            );

            $localityNames = [
                'Civil Lines', 'Prem Nagar', 'Rajendra Nagar', 'Model Town',
                'DD Puram', 'Izzatnagar', 'Subhash Nagar', 'Shahamatganj',
                'Qila', 'Kotwali', 'Suresh Sharma Nagar', 'Green Park',
            ];

            $locations = [];
            foreach ($localityNames as $name) {
                $slug = 'demo-'.Str::slug($name);
                $locations[$name] = CircleRateLocation::query()->firstOrCreate(
                    ['district_id' => $district->id, 'parent_id' => null, 'slug' => $slug],
                    [
                        'tehsil_id' => $tehsil->id,
                        'area_type_id' => $urban->id,
                        'level_id' => $localityLevel->id,
                        'name' => $name.' (DEMO)',
                        'attributes' => [
                            'dataset' => 'DEMO',
                            'notice' => 'Synthetic city locality for calculator testing; not a notified valuation zone.',
                        ],
                        'is_active' => true,
                    ]
                );
                $this->assertDemoMarker(($locations[$name]->attributes['dataset'] ?? null) === 'DEMO', 'location '.$name);
            }

            $mohallaLevel = CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => 'mohalla'],
                ['name' => 'Mohalla', 'description' => 'Urban mohalla level.', 'is_required' => false, 'is_active' => true, 'sort_order' => 2]
            );
            $wardLevel = CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => 'ward'],
                ['name' => 'Ward', 'description' => 'Urban ward level.', 'is_required' => false, 'is_active' => true, 'sort_order' => 3]
            );

            $childLocations = [
                ['Civil Lines', 'Civil Lines Central Mohalla', 'mohalla', $mohallaLevel],
                ['Prem Nagar', 'Prem Nagar Extension', 'mohalla', $mohallaLevel],
                ['Rajendra Nagar', 'Rajendra Nagar Ward 1', 'ward', $wardLevel],
                ['Model Town', 'Model Town Ward 2', 'ward', $wardLevel],
            ];
            foreach ($childLocations as [$parentName, $childName, $kind, $level]) {
                $parent = $locations[$parentName];
                $child = CircleRateLocation::query()->firstOrCreate(
                    ['district_id' => $district->id, 'parent_id' => $parent->id, 'slug' => 'demo-'.Str::slug($childName)],
                    [
                        'tehsil_id' => $tehsil->id,
                        'area_type_id' => $urban->id,
                        'level_id' => $level->id,
                        'name' => $childName.' (DEMO)',
                        'attributes' => ['dataset' => 'DEMO', 'location_kind' => $kind],
                        'is_active' => true,
                    ]
                );
                $this->assertDemoMarker(($child->attributes['dataset'] ?? null) === 'DEMO', 'child location '.$childName);
            }

            $typeDefinitions = [
                ['agricultural_land', 'Agricultural Land (DEMO)', 'agricultural_land'],
                ['residential_plot', 'Residential Plot (DEMO)', 'residential_plot'],
                ['commercial_plot', 'Commercial Plot (DEMO)', 'commercial_plot'],
                ['residential_building', 'Residential Building (DEMO)', 'building_area'],
                ['commercial_building', 'Commercial Building (DEMO)', 'building_area'],
                ['industrial_property', 'Industrial Property (DEMO)', 'industrial_plot'],
                ['mixed_use_property', 'Mixed-Use Property (DEMO)', 'mixed_use_land'],
            ];

            $types = [];
            foreach ($typeDefinitions as $index => [$slug, $name, $strategy]) {
                $types[$slug] = CircleRatePropertyType::query()->firstOrCreate(
                    ['slug' => 'demo-'.$slug],
                    [
                        'name' => $name,
                        'description' => self::DEMO_NOTE,
                        'calculation_strategy' => $strategy,
                        'field_configuration' => ['dataset' => 'DEMO'],
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
                $this->assertDemoMarker(str_contains((string) $types[$slug]->description, self::DEMO_NOTE), 'property type '.$slug);
            }

            $categoryDefinitions = [
                ['main-road', 'Main Road (DEMO)', ['residential_plot', 'commercial_plot', 'commercial_building', 'mixed_use_property']],
                ['internal-road', 'Internal Road (DEMO)', ['residential_plot', 'residential_building', 'mixed_use_property']],
                ['corner-property', 'Corner Property (DEMO)', ['residential_plot', 'commercial_plot', 'residential_building', 'commercial_building', 'mixed_use_property']],
                ['residential-colony', 'Residential Colony (DEMO)', ['residential_plot', 'residential_building', 'mixed_use_property']],
                ['commercial-market', 'Commercial Market (DEMO)', ['commercial_plot', 'commercial_building', 'mixed_use_property']],
                ['developed-area', 'Developed Area (DEMO)', ['residential_plot', 'commercial_plot', 'residential_building', 'commercial_building', 'industrial_property', 'mixed_use_property']],
                ['undeveloped-area', 'Undeveloped Area (DEMO)', ['agricultural_land', 'residential_plot', 'industrial_property', 'mixed_use_property']],
                ['agricultural-category', 'Agricultural Category (DEMO)', ['agricultural_land']],
            ];

            $categories = [];
            foreach ($categoryDefinitions as $index => [$slug, $name, $applicableTypes]) {
                $category = CircleRatePropertyCategory::query()->firstOrCreate(
                    ['slug' => 'demo-'.$slug],
                    [
                        'name' => $name,
                        'description' => self::DEMO_NOTE,
                        'priority' => 100 - $index,
                        'applicability' => ['dataset' => 'DEMO', 'property_type_slugs' => $applicableTypes],
                        'is_active' => true,
                    ]
                );
                $this->assertDemoMarker(str_contains((string) $category->description, self::DEMO_NOTE), 'property category '.$slug);
                $categories[$slug] = $category;
                $category->propertyTypes()->syncWithoutDetaching(
                    collect($applicableTypes)->map(fn (string $typeSlug) => $types[$typeSlug]->id)->all()
                );
            }

            // Square-foot base factors. Bigha is intentionally configurable demo data,
            // not an asserted local statutory conversion.
            $unitDefinitions = [
                ['square-foot', 'Square Foot (DEMO)', 'sq ft', 1.0, true],
                ['square-metre', 'Square Metre (DEMO)', 'sq m', 10.7639, false],
                ['acre', 'Acre (DEMO)', 'acre', 43560.0, false],
                ['hectare', 'Hectare (DEMO)', 'ha', 107639.0, false],
                ['bigha', 'Bigha (CONFIGURABLE DEMO)', 'bigha', 27225.0, false],
            ];

            $units = [];
            foreach ($unitDefinitions as [$slug, $name, $symbol, $baseFactor, $isBase]) {
                $units[$slug] = CircleRateUnit::query()->firstOrCreate(
                    ['slug' => 'demo-'.$slug],
                    [
                        'name' => $name,
                        'symbol' => $symbol,
                        'dimension' => 'area',
                        'base_factor' => $baseFactor,
                        'is_base_unit' => $isBase,
                        'is_active' => true,
                    ]
                );
                $this->assertDemoMarker(str_contains((string) $units[$slug]->name, 'DEMO'), 'rate unit '.$slug);
            }

            // Road widths are measured in metres; this is a length unit, not a
            // calculator area unit.
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
            $this->assertDemoMarker(str_contains((string) $metre->name, 'DEMO'), 'road-width metre unit');

            $unitList = array_values($units);
            foreach ($unitList as $from) {
                foreach ($unitList as $to) {
                    if ($from->id === $to->id) {
                        continue;
                    }
                    $factor = (float) $from->base_factor / (float) $to->base_factor;
                    CircleRateUnitConversion::query()->firstOrCreate(
                        ['from_unit_id' => $from->id, 'to_unit_id' => $to->id],
                        [
                            'factor' => $factor,
                            'source_note' => self::DEMO_NOTE.'; configurable sample factor only; requires administrator verification before operational use.',
                            'is_verified' => false,
                        ]
                    );
                    $conversion = CircleRateUnitConversion::query()->where('from_unit_id', $from->id)->where('to_unit_id', $to->id)->firstOrFail();
                    $this->assertDemoMarker(! $conversion->is_verified && str_contains((string) $conversion->source_note, self::DEMO_NOTE), 'unit conversion');
                }
            }

            $roadDefinitions = [
                ['Below 6 metres (DEMO)', 0, 5.9999, 1],
                ['6–9 metres (DEMO)', 6, 8.9999, 2],
                ['9–12 metres (DEMO)', 9, 11.9999, 3],
                ['12–18 metres (DEMO)', 12, 18, 4],
                ['Above 18 metres (DEMO)', 18.0001, null, 5],
            ];

            $roadWidths = [];
            foreach ($roadDefinitions as [$name, $minimum, $maximum, $priority]) {
                $roadWidths[$priority] = CircleRateRoadWidthRange::query()->firstOrCreate(
                    ['name' => $name],
                    [
                        'unit_id' => $metre->id,
                        'minimum_width' => $minimum,
                        'maximum_width' => $maximum,
                        'applicability' => ['dataset' => 'DEMO'],
                        'adjustment_configuration' => ['dataset' => 'DEMO', 'rate_multiplier' => 1],
                        'priority' => $priority,
                        'is_active' => true,
                    ]
                );
                $this->assertDemoMarker(str_contains((string) $roadWidths[$priority]->name, 'DEMO'), 'road-width range '.$priority);
            }

            $yearDefinitions = [
                ['2025-26', '2025-04-01', '2026-03-31'],
                ['2026-27', '2026-04-01', '2027-03-31'],
            ];
            $years = [];
            foreach ($yearDefinitions as [$name, $startsOn, $endsOn]) {
                $years[$name] = CircleRateFinancialYear::query()->firstOrCreate(
                    ['name' => $name],
                    [
                        'starts_on' => $startsOn,
                        'ends_on' => $endsOn,
                        'is_current' => $name === '2026-27',
                        'is_active' => true,
                        'show_on_frontend' => true,
                    ]
                );
            }

            $source = CircleRateSource::query()
                ->where('reference_number', 'DEMO-BAREILLY-CITY-CIRCLE-RATES')
                ->first();

            if (! $source) {
                $source = CircleRateSource::query()->create([
                    'title' => self::DEMO_NOTE,
                    'reference_number' => 'DEMO-BAREILLY-CITY-CIRCLE-RATES',
                    'document_path' => null,
                    'document_disk' => 'public',
                    'source_url' => null,
                    'published_on' => null,
                    'notes' => self::DEMO_NOTE.'; Synthetic Bareilly city test dataset generated by BareillyCityDemoSeeder. This is not an official government notification, valuation zone, source document or circle-rate schedule.',
                ]);
            }
            $this->assertDemoMarker($source->title === self::DEMO_NOTE && str_contains((string) $source->notes, self::DEMO_NOTE), 'rate source');

            $demoCategoryMap = [
                'agricultural_land' => ['undeveloped-area', 'agricultural-category'],
                'residential_plot' => ['main-road', 'internal-road'],
                'commercial_plot' => ['corner-property', 'commercial-market'],
                'residential_building' => ['residential-colony', 'developed-area'],
                'commercial_building' => ['main-road', 'commercial-market'],
                'industrial_property' => ['undeveloped-area', 'developed-area'],
                'mixed_use_property' => ['corner-property', 'residential-colony'],
            ];

            $locationList = array_values($locations);
            $typeList = array_values($types);
            $roadList = array_values($roadWidths);
            $yearList = array_values($years);
            $unitList = array_values($units);
            $baseRates = [95, 1250, 1850, 1650, 2450, 720, 1380];

            // Fourteen representative rate records cover every property type and both
            // financial years while rotating across all categories, road bands and units.
            foreach ($typeList as $typeIndex => $type) {
                $typeSlug = array_keys($types)[$typeIndex];
                foreach ($yearList as $yearIndex => $year) {
                    $sequence = ($typeIndex * 2 + $yearIndex);
                    $categorySlug = $demoCategoryMap[$typeSlug][$yearIndex];
                    $category = $categories[$categorySlug];
                    $road = $roadList[$sequence % count($roadList)];
                    $unit = $unitList[$sequence % count($unitList)];
                    $location = $locationList[$sequence % count($locationList)];

                    $identity = [
                        'district_id' => $district->id,
                        'tehsil_id' => $tehsil->id,
                        'location_id' => $location->id,
                        'area_type_id' => $urban->id,
                        'property_type_id' => $type->id,
                        'category_id' => $category->id,
                        'road_width_range_id' => $road->id,
                        'financial_year_id' => $year->id,
                        'unit_id' => $unit->id,
                        'source_id' => $source->id,
                        'effective_from' => $year->starts_on->toDateString(),
                    ];

                    $matchIdentity = $identity;
                    unset($matchIdentity['source_id']);
                    $existing = CircleRate::query()
                        ->where($matchIdentity)
                        ->first();

                    // Do not alter or shadow an existing manually managed/imported rate.
                    if ($existing && (
                        (int) $existing->source_id !== (int) $source->id
                        || ! str_contains((string) $existing->admin_notes, self::DEMO_NOTE)
                    )) {
                        continue;
                    }

                    $rate = $existing ?: CircleRate::query()->create($identity + [
                        'rate' => round($baseRates[$typeIndex] * (1 + ($yearIndex * 0.075)) * (1 + (($sequence % 4) * 0.03)), 4),
                        'priority' => 100,
                        'effective_until' => $year->ends_on->toDateString(),
                        'verification_status' => 'pending',
                        'status' => 'draft',
                        'conditions' => [
                            ['field' => 'category_id', 'operator' => 'equals', 'value' => $category->id],
                            ['field' => 'road_width_range_id', 'operator' => 'equals', 'value' => $road->id],
                        ],
                        'admin_notes' => self::DEMO_NOTE.'; synthetic test value only; not eligible as a verified official rate.',
                    ]);

                    CircleRateVersion::query()->firstOrCreate(
                        ['circle_rate_id' => $rate->id, 'version_number' => 1],
                        [
                            'snapshot' => $rate->getAttributes(),
                            'change_reason' => self::DEMO_NOTE.'; initial sample version.',
                        ]
                    );
                }
            }

            $this->seedCalculatorFields($types);
            $this->seedCalculationRules($source, $types, $categories, $roadWidths);

            // An upload-history fixture only. It is not a confirmed import and has no
            // stored source file or imported official rows.
            $hash = hash('sha256', 'BAREILLY-CITY-DEMO-TEST-DATA-V1');
            $batch = CircleRateImportBatch::query()->firstOrCreate(
                ['file_hash' => $hash],
                [
                    'financial_year_id' => $years['2026-27']->id,
                    'source_id' => $source->id,
                    'original_filename' => 'DEMO-BAREILLY-CITY-TEST-DATA.csv',
                    'stored_path' => null,
                    'file_type' => 'csv',
                    'status' => 'uploaded',
                    'column_mapping' => ['dataset' => 'DEMO'],
                    'total_rows' => 0,
                    'valid_rows' => 0,
                    'invalid_rows' => 0,
                    'conflict_rows' => 0,
                    'summary' => self::DEMO_NOTE.'; metadata-only import history fixture; no import was confirmed.',
                ]
            );
            $this->assertDemoMarker(
                str_starts_with((string) $batch->original_filename, 'DEMO-')
                && str_contains((string) $batch->summary, self::DEMO_NOTE),
                'import batch fixture'
            );
        });
    }

    /**
     * @param array<string, CircleRatePropertyType> $types
     */
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

        foreach ($definitions as $index => [$key, $label, $inputType, $options, $applicableTypeSlugs]) {
            $applicableIds = $applicableTypeSlugs === null
                ? []
                : collect($applicableTypeSlugs)->map(fn (string $slug) => $types[$slug]->id)->all();

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
                    'visibility_conditions' => $applicableIds
                        ? [['field' => 'property_type_id', 'operator' => 'in', 'value' => $applicableIds]]
                        : [],
                    'applicability' => $applicableIds ? ['property_type_ids' => $applicableIds] : [],
                    'default_value' => null,
                    'is_required' => false,
                    'is_active' => true,
                    'sort_order' => 900 + $index,
                ]
            );
            $this->assertDemoMarker(str_contains((string) $field->help_text, self::DEMO_NOTE), 'calculator field '.$key);

            foreach ($options as $optionIndex => $option) {
                $value = Str::slug($option, '_');
                CircleRateCalculatorFieldOption::query()->firstOrCreate(
                    ['field_id' => $field->id, 'value' => $value],
                    [
                        'label' => $option,
                        'sort_order' => $optionIndex + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * @param array<string, CircleRatePropertyType> $types
     * @param array<string, CircleRatePropertyCategory> $categories
     * @param array<int, CircleRateRoadWidthRange> $roadWidths
     */
    private function seedCalculationRules(CircleRateSource $source, array $types, array $categories, array $roadWidths): void
    {
        $rules = [
            ['agricultural-valuation', 'Agricultural valuation sample adjustment', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['agricultural_land']->id]], ['rate_multiplier' => 1.02], 100],
            ['residential-valuation', 'Residential valuation sample adjustment', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['residential_plot']->id, $types['residential_building']->id]]], ['rate_multiplier' => 1.01], 90],
            ['commercial-valuation', 'Commercial valuation sample adjustment', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['commercial_plot']->id, $types['commercial_building']->id]]], ['rate_multiplier' => 1.03], 80],
            ['industrial-valuation', 'Industrial valuation sample adjustment', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['industrial_property']->id]], ['rate_multiplier' => 1.015], 70],
            ['mixed-use-valuation', 'Mixed-use valuation sample adjustment', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['mixed_use_property']->id]], ['rate_multiplier' => 1.025], 60],
            ['road-width-condition', 'Road-width condition sample rule', [['field' => 'road_width_range_id', 'operator' => 'in', 'value' => collect($roadWidths)->pluck('id')->all()]], ['rate_multiplier' => 1.0], 50],
            ['category-condition', 'Category condition sample rule', [['field' => 'category_id', 'operator' => 'in', 'value' => collect($categories)->pluck('id')->all()]], ['rate_multiplier' => 1.0], 40],
            ['corner-plot-condition', 'Corner plot conditional sample rule', [['field' => 'custom.demo_corner_plot', 'operator' => 'equals', 'value' => true]], ['rate_multiplier' => 1.025], 30],
            ['require-building-details', 'Building fields required sample rule', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['residential_building']->id, $types['commercial_building']->id]]], ['require_fields' => ['custom.demo_construction_type', 'custom.demo_building_age']], 20],
        ];

        foreach ($rules as [$slug, $description, $conditions, $actions, $priority]) {
            $name = 'DEMO – Bareilly City – '.$slug;
            CircleRateRule::query()->firstOrCreate(
                ['name' => $name],
                [
                    'description' => self::DEMO_NOTE.'; '.$description,
                    'conditions' => $conditions,
                    'actions' => $actions,
                    'source_id' => $source->id,
                    'is_verified' => false,
                    'verified_by' => null,
                    'verified_at' => null,
                    'priority' => $priority,
                    'stop_processing' => false,
                    'is_active' => true,
                    'effective_from' => '2025-04-01',
                    'effective_until' => '2027-03-31',
                ]
            );
            $rule = CircleRateRule::query()->where('name', $name)->firstOrFail();
            $this->assertDemoMarker(! $rule->is_verified && str_contains((string) $rule->description, self::DEMO_NOTE), 'calculation rule '.$slug);
        }
    }

    private function assertDemoMarker(bool $valid, string $record): void
    {
        if (! $valid) {
            throw new \RuntimeException('Refusing to reuse a non-demo record for '.$record.'. No existing official or manually managed data was changed.');
        }
    }
}
