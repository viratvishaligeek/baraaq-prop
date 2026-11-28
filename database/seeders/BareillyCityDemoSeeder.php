<?php

namespace Database\Seeders;

use App\Models\CircleRate;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateAuditLog;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BareillyCityDemoSeeder extends Seeder
{
    private const PREFIX = 'DEMO – Bareilly City';
    private array $records = [];

    public function run(): void
    {
        DB::transaction(function (): void {
            $district = CircleRateDistrict::query()->firstOrCreate(
                ['slug' => 'bareilly'],
                ['name' => 'Bareilly', 'state' => 'Uttar Pradesh', 'is_active' => true]
            );
            $tehsil = CircleRateTehsil::query()->firstOrCreate(
                ['district_id' => $district->id, 'slug' => 'bareilly'],
                ['name' => 'Bareilly', 'is_active' => true]
            );

            $urban = $this->demo(CircleRateAreaType::class, ['slug' => 'demo-urban-city'], [
                'name' => 'Urban (Demo)', 'description' => 'DEMO city-only area type; not an officially notified valuation zone.',
                'is_active' => true, 'sort_order' => 90,
            ]);
            $levels = [];
            foreach (['Locality', 'Sub Area', 'Mohalla', 'Ward'] as $i => $name) {
                $levels[$name] = $this->demo(CircleRateLocationLevel::class, ['slug' => 'demo-'.Str::slug($name)], [
                    'name' => $name.' (Demo)', 'description' => 'DEMO hierarchy level for Bareilly city testing.',
                    'is_required' => false, 'is_active' => true, 'sort_order' => 90 + $i,
                ]);
            }

            $localityNames = [
                'Civil Lines', 'Prem Nagar', 'Rajendra Nagar', 'Model Town', 'DD Puram',
                'Izzatnagar', 'Subhash Nagar', 'Shahamatganj', 'Qila', 'Kotwali',
                'Suresh Sharma Nagar', 'Green Park', 'Satellite', 'Mahanagar Colony',
            ];
            $locations = [];
            foreach ($localityNames as $i => $name) {
                $slug = 'demo-'.Str::slug($name);
                $locations[$name] = $this->demo(CircleRateLocation::class, [
                    'district_id' => $district->id, 'parent_id' => null, 'slug' => $slug,
                ], [
                    'district_id' => $district->id, 'tehsil_id' => $tehsil->id,
                    'area_type_id' => $urban->id, 'level_id' => $levels['Locality']->id,
                    'parent_id' => null, 'name' => $name.' (Demo)', 'slug' => $slug,
                    'code' => 'BR-D-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'attributes' => ['demo' => true, 'demo_scope' => 'Bareilly city only', 'official_zone' => false],
                    'is_active' => true,
                ]);
            }
            foreach (['Civil Lines', 'Rajendra Nagar', 'Model Town', 'Qila'] as $parentName) {
                foreach (['North', 'South'] as $suffix) {
                    $parent = $locations[$parentName];
                    $slug = 'demo-'.Str::slug($parentName.' '.$suffix);
                    $child = $this->demo(CircleRateLocation::class, [
                        'district_id' => $district->id, 'parent_id' => $parent->id, 'slug' => $slug,
                    ], [
                        'district_id' => $district->id, 'tehsil_id' => $tehsil->id,
                        'area_type_id' => $urban->id, 'level_id' => $levels['Sub Area']->id,
                        'parent_id' => $parent->id, 'name' => $parentName.' '.$suffix.' (Demo)',
                        'slug' => $slug, 'attributes' => ['demo' => true, 'official_zone' => false],
                        'is_active' => true,
                    ]);
                    $this->records['locations'][] = $child->id;
                }
            }
            foreach (['Civil Lines', 'DD Puram', 'Shahamatganj'] as $parentName) {
                $parent = $locations[$parentName];
                $slug = 'demo-mohalla-'.Str::slug($parentName);
                $child = $this->demo(CircleRateLocation::class, [
                    'district_id' => $district->id, 'parent_id' => $parent->id, 'slug' => $slug,
                ], [
                    'district_id' => $district->id, 'tehsil_id' => $tehsil->id,
                    'area_type_id' => $urban->id, 'level_id' => $levels['Mohalla']->id,
                    'parent_id' => $parent->id, 'name' => $parentName.' Mohalla (Demo)',
                    'slug' => $slug, 'attributes' => ['demo' => true, 'official_zone' => false],
                    'is_active' => true,
                ]);
                $this->records['locations'][] = $child->id;
            }

            $types = [];
            $typeRows = [
                ['Agricultural Land', 'agricultural_land', 'agricultural_land'],
                ['Residential Plot', 'residential_plot', 'residential_plot'],
                ['Commercial Plot', 'commercial_plot', 'commercial_plot'],
                ['Residential Building', 'residential_building', 'building_area'],
                ['Commercial Building', 'commercial_building', 'building_area'],
                ['Industrial Property', 'industrial_property', 'industrial_plot'],
                ['Mixed-Use Property', 'mixed_use_property', 'mixed_use_land'],
            ];
            foreach ($typeRows as $i => [$name, $slug, $strategy]) {
                $types[$slug] = $this->demo(CircleRatePropertyType::class, ['slug' => 'demo-'.$slug], [
                    'name' => $name.' (Demo)', 'description' => 'DEMO property type for calculator and matching tests.',
                    'calculation_strategy' => $strategy, 'field_configuration' => ['demo' => true],
                    'formula_configuration' => null, 'is_active' => true, 'sort_order' => 90 + $i,
                ]);
            }

            $categories = [];
            foreach ([
                'Main Road', 'Internal Road', 'Corner Property', 'Residential Colony',
                'Commercial Market', 'Developed Area', 'Undeveloped Area', 'Agricultural Category',
            ] as $i => $name) {
                $slug = 'demo-'.Str::slug($name);
                $categories[$name] = $this->demo(CircleRatePropertyCategory::class, ['slug' => $slug], [
                    'name' => $name.' (Demo)', 'description' => 'DEMO category; not an official classification.',
                    'priority' => 90 - $i, 'applicability' => ['demo' => true], 'is_active' => true,
                ]);
            }
            $typeCategoryMap = [
                'agricultural_land' => ['Agricultural Category', 'Undeveloped Area'],
                'residential_plot' => ['Residential Colony', 'Main Road', 'Internal Road', 'Corner Property', 'Developed Area', 'Undeveloped Area'],
                'commercial_plot' => ['Commercial Market', 'Main Road', 'Corner Property'],
                'residential_building' => ['Residential Colony', 'Developed Area', 'Corner Property'],
                'commercial_building' => ['Commercial Market', 'Main Road', 'Corner Property'],
                'industrial_property' => ['Developed Area', 'Undeveloped Area', 'Main Road'],
                'mixed_use_property' => ['Main Road', 'Commercial Market', 'Corner Property'],
            ];
            foreach ($typeCategoryMap as $typeSlug => $names) {
                $types[$typeSlug]->categories()->syncWithoutDetaching(collect($names)->map(fn ($n) => $categories[$n]->id)->all());
            }
            foreach ($categories as $category) {
                $category->locations()->syncWithoutDetaching(collect($locations)->pluck('id')->all());
            }

            $unitRows = [
                ['Per Square Foot', 'sq ft', 'square-foot', 1.0, true],
                ['Per Square Metre', 'sq m', 'square-metre', 10.7639, false],
                ['Per Acre', 'acre', 'acre', 43560.0, false],
                ['Per Hectare', 'ha', 'hectare', 107639.0, false],
                ['Bigha (Configurable Demo)', 'bigha*', 'demo-bigha', 27000.0, false],
            ];
            $units = [];
            foreach ($unitRows as [$name, $symbol, $slug, $factor, $base]) {
                $units[$slug] = $this->demo(CircleRateUnit::class, ['slug' => $slug], [
                    'name' => $name.' (Demo)', 'symbol' => $symbol, 'dimension' => 'area',
                    'base_factor' => $factor, 'is_base_unit' => $base, 'is_active' => true,
                ]);
            }
            foreach ($units as $fromSlug => $from) {
                foreach ($units as $toSlug => $to) {
                    if ($from->id === $to->id) continue;
                    $conversion = CircleRateUnitConversion::query()->firstOrNew([
                        'from_unit_id' => $from->id, 'to_unit_id' => $to->id,
                    ]);
                    if ($conversion->exists && ! $conversion->is_demo) {
                        throw new RuntimeException('A non-demo unit conversion conflicts with the requested demo conversion.');
                    }
                    $conversion->forceFill([
                        'factor' => (float) $from->base_factor / (float) $to->base_factor,
                        'source_note' => 'DEMO ONLY: configurable testing factor; not an official land-measure conversion.',
                        'is_verified' => true, 'is_demo' => true,
                    ])->save();
                    $this->records['conversions'][] = $conversion->id;
                }
            }

            $roadWidths = [];
            $roadRows = [
                ['Below 6 metres', 0, 6, 5, 50],
                ['6–9 metres', 6, 9, null, 40],
                ['9–12 metres', 9, 12, null, 30],
                ['12–18 metres', 12, 18, null, 20],
                ['Above 18 metres', 18, null, null, 10],
            ];
            foreach ($roadRows as [$name, $min, $max, $exact, $priority]) {
                $slug = 'demo-road-'.Str::slug($name);
                $roadWidths[$name] = $this->demo(CircleRateRoadWidthRange::class, ['name' => $name.' (Demo)'], [
                    'name' => $name.' (Demo)', 'unit_id' => $units['square-metre']->id,
                    'minimum_width' => $min, 'maximum_width' => $max, 'exact_width' => $exact,
                    'applicability' => ['demo' => true], 'adjustment_configuration' => null,
                    'effective_from' => '2025-04-01', 'effective_until' => null,
                    'priority' => $priority, 'is_active' => true,
                ]);
            }

            $years = [];
            foreach ([['2025-26', '2025-04-01', '2026-03-31'], ['2026-27', '2026-04-01', '2027-03-31']] as $i => [$name, $start, $end]) {
                $years[$i] = $this->demo(CircleRateFinancialYear::class, ['name' => $name], [
                    'name' => $name, 'starts_on' => $start, 'ends_on' => $end,
                    'is_current' => false, 'is_active' => true, 'show_on_frontend' => true,
                ]);
            }

            $source = $this->demo(CircleRateSource::class, ['reference_number' => 'DEMO-BR-CITY-2026'], [
                'title' => self::PREFIX.' – SAMPLE RATES ONLY',
                'reference_number' => 'DEMO-BR-CITY-2026',
                'document_path' => null, 'document_disk' => 'public', 'source_url' => null,
                'published_on' => null, 'notes' => 'Synthetic testing data. Not issued, approved or verified by any government authority.',
                'uploaded_by' => null,
            ]);

            $fieldDefinitions = [
                ['land_classification', 'Land Classification', 'dropdown', ['Agricultural', 'Residential', 'Commercial'], ['agricultural_land']],
                ['plot_facing', 'Plot Facing', 'radio', ['North', 'South', 'East', 'West'], ['residential_plot', 'commercial_plot']],
                ['corner_plot', 'Corner Plot', 'checkbox', [], ['demo-residential-plot', 'demo-commercial-plot']],
                ['land_use', 'Land Use', 'select', ['Residential', 'Commercial', 'Mixed'], ['residential_plot', 'commercial_plot', 'mixed_use_property']],
                ['construction_type', 'Construction Type', 'dropdown', ['RCC', 'Load Bearing', 'Temporary'], ['residential_building', 'commercial_building']],
                ['building_age', 'Building Age', 'number', [], ['demo-residential-building', 'demo-commercial-building']],
                ['number_of_floors', 'Number of Floors', 'number', [], ['demo-residential-building', 'demo-commercial-building']],
                ['road_width', 'Road Width', 'number', [], null],
                ['development_status', 'Property Development Status', 'radio', ['Developed', 'Undeveloped'], null],
                ['site_notes', 'Site Notes', 'text', [], null],
                ['possession_date', 'Possession / Reference Date', 'date', [], null],
                ['additional_amenities', 'Additional Amenities', 'multi-select', ['Parking', 'Water', 'Electricity'], ['demo-residential-building', 'demo-commercial-building']],
            ];
            $fields = [];
            foreach ($fieldDefinitions as $i => [$name, $label, $inputType, $options, $applicability]) {
                $field = $this->demo(CircleRateCalculatorField::class, ['name' => 'demo_'.$name], [
                    'name' => 'demo_'.$name, 'label' => $label.' (Demo)', 'input_type' => $inputType,
                    'data_source' => null, 'placeholder' => 'DEMO value for testing',
                    'help_text' => 'DEMO calculator field; values are for testing only.',
                    'validation_rules' => in_array($inputType, ['number'], true) ? ['min' => 0, 'max' => 100] : [],
                    'visibility_conditions' => $name === 'construction_type' || $name === 'building_age' || $name === 'number_of_floors'
                        ? [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['residential_building']->id, $types['commercial_building']->id]]]
                        : [],
                    'applicability' => ['property_type_ids' => $applicability ? collect($applicability)->map(fn ($slug) => $types[$slug]->id)->all() : collect($types)->pluck('id')->all(), 'demo' => true],
                    'default_value' => null, 'is_required' => false, 'is_active' => true, 'sort_order' => 90 + $i,
                ]);
                $fields[$name] = $field;
                foreach ($options as $j => $option) {
                    $this->demo(CircleRateCalculatorFieldOption::class, ['field_id' => $field->id, 'value' => Str::slug($option)], [
                        'field_id' => $field->id, 'label' => $option.' (Demo)', 'value' => Str::slug($option),
                        'sort_order' => $j + 1, 'is_active' => true,
                    ]);
                }
            }

            $rateUnits = array_values($units);
            $rateCount = 0;
            foreach ($typeRows as $typeIndex => [$typeName, $typeSlug]) {
                foreach ($years as $yearIndex => $year) {
                    $location = array_values($locations)[$typeIndex % count($locations)];
                    $categoryNames = $typeCategoryMap[$typeSlug];
                    $category = $categories[$categoryNames[0]];
                    $road = array_values($roadWidths)[$typeIndex % count($roadWidths)];
                    $unit = $rateUnits[($typeIndex + $yearIndex) % count($rateUnits)];
                    $identity = [
                        'district_id' => $district->id, 'location_id' => $location->id,
                        'property_type_id' => $types[$typeSlug]->id, 'category_id' => $category->id,
                        'road_width_range_id' => $road->id, 'financial_year_id' => $year->id,
                        'unit_id' => $unit->id, 'priority' => 100,
                    ];
                    $rate = CircleRate::query()->firstOrNew($identity);
                    if ($rate->exists && ! $rate->is_demo) {
                        throw new RuntimeException('A non-demo circle rate conflicts with the requested demo rate. No official record was changed.');
                    }
                    $amount = [120, 185, 260, 340, 420, 95, 290][$typeIndex] * (1 + ($yearIndex * 0.08)) * (1 + (($typeIndex % 3) * 0.03));
                    $rate->forceFill($identity + [
                        'tehsil_id' => $tehsil->id, 'area_type_id' => $urban->id,
                        'source_id' => $source->id, 'rate' => round($amount, 4),
                        'effective_from' => $year->starts_on->toDateString(),
                        'effective_until' => $year->ends_on->toDateString(),
                        'verification_status' => 'pending', 'status' => 'published',
                        'conditions' => [['field' => 'road_width_range_id', 'operator' => 'equals', 'value' => $road->id]],
                        'admin_notes' => 'DEMO ONLY – synthetic sample rate; never an official government circle rate.',
                        'created_by' => null, 'verified_by' => null, 'verified_at' => null, 'is_demo' => true,
                    ])->save();
                    $this->records['rates'][] = $rate->id;
                    $this->records['locations'][] = $location->id;
                    $rateCount++;
                    if (! $rate->versions()->where('is_demo', true)->exists()) {
                        $version = CircleRateVersion::query()->create([
                            'circle_rate_id' => $rate->id, 'version_number' => 1,
                            'snapshot' => $rate->getAttributes(), 'change_reason' => 'DEMO seed baseline version',
                            'changed_by' => null, 'is_demo' => true,
                        ]);
                        $this->records['versions'][] = $version->id;
                    }
                }
            }

            $ruleRows = [
                ['Agricultural demo valuation', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['agricultural_land']->id]], ['require_fields' => ['custom.demo_land_classification']], 100],
                ['Residential demo valuation adjustment', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['residential_plot']->id]], ['rate_multiplier' => 1.05], 90],
                ['Commercial demo valuation adjustment', [['field' => 'property_type_id', 'operator' => 'in', 'value' => [$types['commercial_plot']->id, $types['commercial_building']->id]]], ['rate_multiplier' => 1.12], 80],
                ['Building demo construction input', [['field' => 'property_type_id', 'operator' => 'equals', 'value' => $types['residential_building']->id]], ['require_fields' => ['custom.demo_construction_type']], 70],
                ['Road width demo priority scenario', [['field' => 'road_width_range_id', 'operator' => 'in', 'value' => collect($roadWidths)->pluck('id')->all()]], ['rate_multiplier' => 1.02], 60],
                ['Category demo priority scenario', [['field' => 'category_id', 'operator' => 'in', 'value' => collect($categories)->pluck('id')->all()]], ['fixed_adjustment' => 250], 50],
                ['Demo conditional-field rule', [['field' => 'custom.demo_corner_plot', 'operator' => 'equals', 'value' => true]], ['rate_multiplier' => 1.01], 40],
                ['Demo lower-priority matching rule', [['field' => 'custom.demo_development_status', 'operator' => 'equals', 'value' => 'developed']], ['fixed_adjustment' => 100], 10],
            ];
            foreach ($ruleRows as [$name, $conditions, $actions, $priority]) {
                $rule = $this->demo(CircleRateRule::class, ['name' => self::PREFIX.' – '.$name], [
                    'name' => self::PREFIX.' – '.$name,
                    'description' => 'DEMO rule used only when a DEMO rate is selected. Not an official valuation instruction.',
                    'conditions' => $conditions, 'actions' => $actions, 'source_id' => $source->id,
                    'is_verified' => false, 'verified_by' => null, 'verified_at' => null,
                    'priority' => $priority, 'stop_processing' => false, 'is_active' => true,
                    'effective_from' => '2025-04-01', 'effective_until' => '2027-03-31', 'created_by' => null,
                ]);
                $this->records['rules'][] = $rule->id;
            }

            $batch = $this->demo(CircleRateImportBatch::class, ['file_hash' => hash('sha256', 'bareilly-city-circle-rate-demo-v1')], [
                'financial_year_id' => $years[1]->id, 'source_id' => $source->id, 'uploaded_by' => null,
                'original_filename' => 'BAREILLY-CITY-DEMO-SAMPLE.csv', 'stored_path' => null,
                'file_hash' => hash('sha256', 'bareilly-city-circle-rate-demo-v1'), 'file_type' => 'csv',
                'status' => 'demo_seeded', 'column_mapping' => ['demo' => true], 'total_rows' => $rateCount,
                'valid_rows' => $rateCount, 'invalid_rows' => 0, 'conflict_rows' => 0,
                'summary' => 'DEMO seeder-created records; not a real import and not official rates.',
                'confirmed_at' => null,
            ]);
            $this->records['batch'] = $batch->id;

            CircleRateAuditLog::query()->create([
                'user_id' => auth()->id(), 'subject_type' => 'BareillyCityDemoSeeder',
                'subject_id' => null, 'event' => 'demo_seeded',
                'old_values' => null,
                'new_values' => ['district_id' => $district->id, 'tehsil_id' => $tehsil->id, 'locations' => count($this->records['locations']), 'rates' => $rateCount, 'source_id' => $source->id],
                'reason' => 'Seeded Bareilly city DEMO data only. Synthetic values; not official government circle rates.',
                'source_id' => $source->id, 'ip_address' => null,
            ]);
        });
    }

    private function demo(string $modelClass, array $identity, array $attributes): Model
    {
        /** @var Model $record */
        $record = $modelClass::query()->firstOrNew($identity);
        if ($record->exists && ! (bool) $record->getAttribute('is_demo')) {
            throw new RuntimeException('Refusing to overwrite a non-demo record in '.$modelClass.'.');
        }
        $record->forceFill($attributes + ['is_demo' => true])->save();
        return $record;
    }
}
