<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateAuditLog;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateLocation;
use App\Models\CircleRateLocationLevel;
use App\Models\CircleRatePropertyCategory;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateRoadWidthRange;
use App\Models\CircleRateUnitConversion;
use App\Models\CircleRateRule;
use App\Models\CircleRateCalculatorField;
use App\Models\CircleRateCalculatorFieldOption;
use App\Models\CircleRateSource;
use App\Models\CircleRateTehsil;
use App\Models\CircleRateUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CircleRateMasterController extends Controller
{
    private function definitions(): array
    {
        return [
            'districts' => [
                'title' => 'Districts', 'model' => CircleRateDistrict::class, 'table' => 'circle_rate_districts',
                'columns' => ['name', 'slug', 'code', 'state', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'District name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'code', 'label' => 'Government code', 'type' => 'text'],
                    ['name' => 'state', 'label' => 'State', 'type' => 'text'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'tehsils' => [
                'title' => 'Tehsils', 'model' => CircleRateTehsil::class, 'table' => 'circle_rate_tehsils',
                'columns' => ['district_id', 'name', 'slug', 'code', 'is_active'],
                'fields' => [
                    ['name' => 'district_id', 'label' => 'District', 'type' => 'select', 'options' => CircleRateDistrict::class, 'required' => true],
                    ['name' => 'name', 'label' => 'Tehsil name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'code', 'label' => 'Code', 'type' => 'text'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'area-types' => [
                'title' => 'Area types', 'model' => CircleRateAreaType::class, 'table' => 'circle_rate_area_types',
                'columns' => ['name', 'slug', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'location-levels' => [
                'title' => 'Location levels', 'model' => CircleRateLocationLevel::class, 'table' => 'circle_rate_location_levels',
                'columns' => ['name', 'slug', 'is_required', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Level name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'is_required', 'label' => 'Required level', 'type' => 'boolean'],
                    ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'locations' => [
                'title' => 'Locations', 'model' => CircleRateLocation::class, 'table' => 'circle_rate_locations',
                'columns' => ['name', 'district_id', 'tehsil_id', 'area_type_id', 'level_id', 'parent_id', 'is_active'],
                'fields' => [
                    ['name' => 'district_id', 'label' => 'District', 'type' => 'select', 'options' => CircleRateDistrict::class, 'required' => true],
                    ['name' => 'tehsil_id', 'label' => 'Tehsil', 'type' => 'select', 'options' => CircleRateTehsil::class],
                    ['name' => 'area_type_id', 'label' => 'Area type', 'type' => 'select', 'options' => CircleRateAreaType::class],
                    ['name' => 'level_id', 'label' => 'Location level', 'type' => 'select', 'options' => CircleRateLocationLevel::class],
                    ['name' => 'parent_id', 'label' => 'Parent location', 'type' => 'select', 'options' => CircleRateLocation::class],
                    ['name' => 'name', 'label' => 'Location name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'code', 'label' => 'Code', 'type' => 'text'],
                    ['name' => 'attributes', 'label' => 'Additional attributes (JSON)', 'type' => 'json'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'property-types' => [
                'title' => 'Property types', 'model' => CircleRatePropertyType::class, 'table' => 'circle_rate_property_types',
                'columns' => ['name', 'slug', 'calculation_strategy', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Property type', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'calculation_strategy', 'label' => 'Calculation strategy', 'type' => 'text', 'required' => true],
                    ['name' => 'field_configuration', 'label' => 'Field configuration (JSON)', 'type' => 'json'],
                    ['name' => 'formula_configuration', 'label' => 'Formula configuration (JSON)', 'type' => 'json'],
                    ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'categories' => [
                'title' => 'Property categories', 'model' => CircleRatePropertyCategory::class, 'table' => 'circle_rate_property_categories',
                'columns' => ['name', 'slug', 'priority', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Category', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'priority', 'label' => 'Matching priority', 'type' => 'number'],
                    ['name' => 'applicability', 'label' => 'Applicability (JSON)', 'type' => 'json'],
                    ['name' => 'property_type_ids', 'label' => 'Applicable property types', 'type' => 'multi-select', 'options' => CircleRatePropertyType::class],
                    ['name' => 'location_ids', 'label' => 'Applicable locations', 'type' => 'multi-select', 'options' => CircleRateLocation::class],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'units' => [
                'title' => 'Rate units', 'model' => CircleRateUnit::class, 'table' => 'circle_rate_units',
                'columns' => ['name', 'symbol', 'dimension', 'base_factor', 'is_base_unit', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Unit name', 'type' => 'text', 'required' => true],
                    ['name' => 'symbol', 'label' => 'Symbol', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'dimension', 'label' => 'Dimension', 'type' => 'text', 'required' => true],
                    ['name' => 'base_factor', 'label' => 'Base factor', 'type' => 'number'],
                    ['name' => 'is_base_unit', 'label' => 'Base unit', 'type' => 'boolean'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'financial-years' => [
                'title' => 'Financial years', 'model' => CircleRateFinancialYear::class, 'table' => 'circle_rate_financial_years',
                'columns' => ['name', 'starts_on', 'ends_on', 'is_current', 'is_active', 'show_on_frontend'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Financial year label', 'type' => 'text', 'required' => true],
                    ['name' => 'starts_on', 'label' => 'Start date', 'type' => 'date', 'required' => true],
                    ['name' => 'ends_on', 'label' => 'End date', 'type' => 'date', 'required' => true],
                    ['name' => 'is_current', 'label' => 'Current year', 'type' => 'boolean'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'show_on_frontend', 'label' => 'Show on frontend', 'type' => 'boolean'],
                ],
            ],
            'unit-conversions' => [
                'title' => 'Unit conversions', 'model' => CircleRateUnitConversion::class, 'table' => 'circle_rate_unit_conversions',
                'columns' => ['from_unit_id', 'to_unit_id', 'factor', 'is_verified'],
                'fields' => [
                    ['name' => 'from_unit_id', 'label' => 'From unit', 'type' => 'select', 'options' => CircleRateUnit::class, 'required' => true],
                    ['name' => 'to_unit_id', 'label' => 'To unit', 'type' => 'select', 'options' => CircleRateUnit::class, 'required' => true],
                    ['name' => 'factor', 'label' => 'Conversion factor', 'type' => 'number', 'required' => true],
                    ['name' => 'source_note', 'label' => 'Conversion source / evidence', 'type' => 'textarea'],
                    ['name' => 'is_verified', 'label' => 'Verified conversion', 'type' => 'boolean'],
                ],
            ],
            'rules' => [
                'title' => 'Rate conditions and rules', 'model' => CircleRateRule::class, 'table' => 'circle_rate_rules',
                'columns' => ['name', 'priority', 'stop_processing', 'is_active', 'effective_from', 'effective_until'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Rule name', 'type' => 'text', 'required' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'conditions', 'label' => 'Conditions JSON', 'type' => 'json', 'required' => true],
                    ['name' => 'actions', 'label' => 'Actions JSON', 'type' => 'json', 'required' => true],
                    ['name' => 'source_id', 'label' => 'Official source document', 'type' => 'select', 'options' => CircleRateSource::class, 'option_label' => 'title'],
                    ['name' => 'is_verified', 'label' => 'Verified rule (authorized verifier only)', 'type' => 'boolean'],
                    ['name' => 'priority', 'label' => 'Priority', 'type' => 'number'],
                    ['name' => 'stop_processing', 'label' => 'Stop after match', 'type' => 'boolean'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'effective_from', 'label' => 'Effective from', 'type' => 'date'],
                    ['name' => 'effective_until', 'label' => 'Effective until', 'type' => 'date'],
                ],
            ],
            'calculator-fields' => [
                'title' => 'Calculator fields', 'model' => CircleRateCalculatorField::class, 'table' => 'circle_rate_calculator_fields',
                'columns' => ['name', 'label', 'input_type', 'is_required', 'is_active', 'sort_order'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Field key', 'type' => 'text', 'required' => true],
                    ['name' => 'label', 'label' => 'Field label', 'type' => 'text', 'required' => true],
                    ['name' => 'input_type', 'label' => 'Input type', 'type' => 'text', 'required' => true],
                    ['name' => 'data_source', 'label' => 'Data source', 'type' => 'text'],
                    ['name' => 'placeholder', 'label' => 'Placeholder', 'type' => 'text'],
                    ['name' => 'help_text', 'label' => 'Help text', 'type' => 'textarea'],
                    ['name' => 'validation_rules', 'label' => 'Validation rules JSON', 'type' => 'json'],
                    ['name' => 'visibility_conditions', 'label' => 'Visibility conditions JSON', 'type' => 'json'],
                    ['name' => 'applicability', 'label' => 'Applicability JSON', 'type' => 'json'],
                    ['name' => 'default_value', 'label' => 'Default value', 'type' => 'text'],
                    ['name' => 'is_required', 'label' => 'Required', 'type' => 'boolean'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                    ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
                ],
            ],
            'field-options' => [
                'title' => 'Calculator field options', 'model' => CircleRateCalculatorFieldOption::class, 'table' => 'circle_rate_calculator_field_options',
                'columns' => ['field_id', 'label', 'value', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'field_id', 'label' => 'Calculator field', 'type' => 'select', 'options' => CircleRateCalculatorField::class, 'required' => true],
                    ['name' => 'label', 'label' => 'Option label', 'type' => 'text', 'required' => true],
                    ['name' => 'value', 'label' => 'Option value', 'type' => 'text', 'required' => true],
                    ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
            'road-widths' => [
                'title' => 'Road width ranges', 'model' => CircleRateRoadWidthRange::class, 'table' => 'circle_rate_road_width_ranges',
                'columns' => ['name', 'unit_id', 'minimum_width', 'maximum_width', 'exact_width', 'priority', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Range name', 'type' => 'text', 'required' => true],
                    ['name' => 'unit_id', 'label' => 'Width unit', 'type' => 'select', 'options' => CircleRateUnit::class, 'required' => true],
                    ['name' => 'minimum_width', 'label' => 'Minimum width', 'type' => 'number'],
                    ['name' => 'maximum_width', 'label' => 'Maximum width', 'type' => 'number'],
                    ['name' => 'exact_width', 'label' => 'Exact width', 'type' => 'number'],
                    ['name' => 'applicability', 'label' => 'Applicability (JSON)', 'type' => 'json'],
                    ['name' => 'adjustment_configuration', 'label' => 'Adjustment configuration (JSON)', 'type' => 'json'],
                    ['name' => 'effective_from', 'label' => 'Effective from', 'type' => 'date'],
                    ['name' => 'effective_until', 'label' => 'Effective until', 'type' => 'date'],
                    ['name' => 'priority', 'label' => 'Priority', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
                ],
            ],
        ];
    }

    private function definition(string $module): array
    {
        abort_unless(isset($this->definitions()[$module]), 404);
        return $this->definitions()[$module];
    }

    public function index(string $module)
    {
        $definition = $this->definition($module);
        $model = $definition['model'];
        $record = request()->filled('edit') ? $model::query()->findOrFail((int) request('edit')) : null;
        if ($record && $module === 'categories') {
            $record->setAttribute('property_type_ids', $record->propertyTypes()->pluck('circle_rate_property_types.id')->all());
            $record->setAttribute('location_ids', $record->locations()->pluck('circle_rate_locations.id')->all());
        }
        $options = [];

        foreach ($definition['fields'] as $field) {
            if (! empty($field['options'])) {
                $optionModel = $field['options'];
                $labelColumn = $field['option_label'] ?? 'name';
                $options[$field['name']] = $optionModel::query()->orderBy($labelColumn)->get(['id', $labelColumn])->map(function ($option) use ($labelColumn) {
                    $option->name = $option->{$labelColumn};
                    return $option;
                });
            }
        }

        $records = $model::query()->latest('id')->paginate(20)->withQueryString();

        return view('admin.circle-rates.master.index', compact('module', 'definition', 'record', 'records', 'options'));
    }

    public function store(Request $request, string $module)
    {
        $definition = $this->definition($module);
        $model = $definition['model'];
        $data = $this->validated($request, $definition);
        $propertyTypeIds = $data['property_type_ids'] ?? null;
        $locationIds = $data['location_ids'] ?? null;
        unset($data['property_type_ids'], $data['location_ids']);
        $record = DB::transaction(function () use ($model, $data, $module, $propertyTypeIds, $locationIds) {
            $created = $model::query()->create($data);
            if ($module === 'categories') {
                $created->propertyTypes()->sync($propertyTypeIds ?? []);
                $created->locations()->sync($locationIds ?? []);
            }
            if ($created instanceof CircleRateFinancialYear && $created->is_current) {
                CircleRateFinancialYear::query()->whereKeyNot($created->id)->update(['is_current' => false]);
            }
            return $created;
        });
        $this->audit($request, $record, 'created', null, $record->getAttributes());

        return redirect()->route('admin.circle-rates.master.index', $module)->with('success', $definition['title'].' record created.');
    }

    public function update(Request $request, string $module, int $id)
    {
        $definition = $this->definition($module);
        $model = $definition['model'];
        $record = $model::query()->findOrFail($id);
        $data = $this->validated($request, $definition, $record);
        $oldValues = $record->getAttributes();
        $propertyTypeIds = $data['property_type_ids'] ?? null;
        $locationIds = $data['location_ids'] ?? null;
        unset($data['property_type_ids'], $data['location_ids']);
        DB::transaction(function () use ($record, $data, $module, $propertyTypeIds, $locationIds): void {
            $record->update($data);
            if ($module === 'categories') {
                $record->propertyTypes()->sync($propertyTypeIds ?? []);
                $record->locations()->sync($locationIds ?? []);
            }
            if ($record instanceof CircleRateFinancialYear && $record->is_current) {
                CircleRateFinancialYear::query()->whereKeyNot($record->id)->update(['is_current' => false]);
            }
        });
        $this->audit($request, $record, 'updated', $oldValues, $record->fresh()->getAttributes());

        return redirect()->route('admin.circle-rates.master.index', $module)->with('success', $definition['title'].' record updated.');
    }

    public function destroy(Request $request, string $module, int $id)
    {
        $definition = $this->definition($module);
        $model = $definition['model'];
        $record = $model::query()->findOrFail($id);

        $oldValues = $record->getAttributes();
        try {
            $record->delete();
        } catch (QueryException) {
            return back()->withErrors(['delete' => 'This record is referenced by other circle-rate data and cannot be deleted. Deactivate it instead.']);
        }

        $this->audit($request, $record, 'deleted', $oldValues, ['deleted_at' => now()->toDateTimeString()]);

        return redirect()->route('admin.circle-rates.master.index', $module)->with('success', $definition['title'].' record deleted.');
    }

    private function audit(Request $request, Model $record, string $event, ?array $old, ?array $new): void
    {
        CircleRateAuditLog::query()->create([
            'user_id' => $request->user()->id,
            'subject_type' => $record::class,
            'subject_id' => $record->getKey(),
            'event' => 'master_'.$event,
            'old_values' => $old,
            'new_values' => $new,
            'reason' => $request->input('change_reason'),
            'source_id' => $record->getAttribute('source_id'),
            'ip_address' => $request->ip(),
        ]);
    }

    private function validated(Request $request, array $definition, ?Model $record = null): array
    {
        if ($request->filled('name') && ! $request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->input('name'))]);
        }

        $rules = [];
        foreach ($definition['fields'] as $field) {
            $name = $field['name'];
            $base = match ($field['type']) {
                'boolean' => ['nullable', 'boolean'],
                'number' => ['nullable', 'numeric'],
                'date' => ['nullable', 'date'],
                'select' => ['nullable', 'integer'],
                'json' => ['nullable', 'json'],
                'multi-select' => ['nullable', 'array'],
                'textarea' => ['nullable', 'string', 'max:10000'],
                default => ['nullable', 'string', 'max:1000'],
            };
            if (! empty($field['required'])) {
                $base[0] = 'required';
            }
            if ($name === 'slug') {
                $uniqueSlug = Rule::unique($definition['table'], 'slug');
                if ($record) $uniqueSlug->ignore($record->getKey());
                $base = ['nullable', 'string', 'max:180', $uniqueSlug];
            }
            if ($name === 'district_id') $base = ['required', 'integer', 'exists:circle_rate_districts,id'];
            if ($name === 'tehsil_id') $base = ['nullable', 'integer', 'exists:circle_rate_tehsils,id'];
            if ($name === 'area_type_id') $base = ['nullable', 'integer', 'exists:circle_rate_area_types,id'];
            if ($name === 'level_id') $base = ['nullable', 'integer', 'exists:circle_rate_location_levels,id'];
            if ($name === 'parent_id') $base = ['nullable', 'integer', 'exists:circle_rate_locations,id'];
            if ($name === 'property_type_ids') $base = ['nullable', 'array'];
            if ($name === 'location_ids') $base = ['nullable', 'array'];
            if ($name === 'unit_id' || $name === 'from_unit_id' || $name === 'to_unit_id') $base = ['required', 'integer', 'exists:circle_rate_units,id'];
            if ($name === 'field_id') $base = ['required', 'integer', 'exists:circle_rate_calculator_fields,id'];
            if ($name === 'source_id') $base = ['nullable', 'integer', 'exists:circle_rate_sources,id'];
            if ($name === 'factor') $base = ['required', 'numeric', 'gt:0'];
            if ($name === 'property_type_ids.*') $base = ['integer', 'exists:circle_rate_property_types,id'];
            if ($name === 'location_ids.*') $base = ['integer', 'exists:circle_rate_locations,id'];
            if ($name === 'starts_on' || $name === 'ends_on') $base = ['required', 'date'];
            $rules[$name] = $base;
            if ($name === 'property_type_ids') $rules['property_type_ids.*'] = ['integer', 'exists:circle_rate_property_types,id'];
            if ($name === 'location_ids') $rules['location_ids.*'] = ['integer', 'exists:circle_rate_locations,id'];
        }

        $data = Validator::make($request->all(), $rules)->validate();
        foreach ($definition['fields'] as $field) {
            $name = $field['name'];
            if ($field['type'] === 'boolean') $data[$name] = $request->boolean($name);
            if ($field['type'] === 'json' && isset($data[$name])) $data[$name] = json_decode($data[$name], true, 512, JSON_THROW_ON_ERROR);
        }
        if (array_key_exists('name', $data) && empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
        if (isset($data['tehsil_id'], $data['district_id']) && ! CircleRateTehsil::query()->whereKey($data['tehsil_id'])->where('district_id', $data['district_id'])->exists()) {
            throw ValidationException::withMessages(['tehsil_id' => 'The selected tehsil does not belong to the selected district.']);
        }
        if (isset($data['starts_on'], $data['ends_on']) && $data['starts_on'] >= $data['ends_on']) {
            throw ValidationException::withMessages(['ends_on' => 'The financial year end date must be after its start date.']);
        }
        if ($record && isset($data['parent_id']) && (int) $data['parent_id'] === (int) $record->id) {
            throw ValidationException::withMessages(['parent_id' => 'A location cannot be its own parent.']);
        }
        if (isset($data['parent_id'], $data['district_id']) && ! CircleRateLocation::query()->whereKey($data['parent_id'])->where('district_id', $data['district_id'])->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'The parent location must belong to the selected district.']);
        }
        if (array_key_exists('is_verified', $data) && $data['is_verified'] && ! request()->user()->isAdministrator() && ! request()->user()->hasPermission('circle-rates.verify')) {
            throw ValidationException::withMessages(['is_verified' => 'Only an authorized verifier can approve conversions or calculation rules.']);
        }
        if ($definition['model'] === CircleRateRule::class && ! empty($data['is_verified']) && empty($data['source_id'])) {
            throw ValidationException::withMessages(['source_id' => 'Attach an official source document before verifying this rule.']);
        }
        if (array_key_exists('is_verified', $data)) {
            $data['verified_by'] = $data['is_verified'] ? request()->user()->id : null;
            $data['verified_at'] = $data['is_verified'] ? now() : null;
        }
        return $data;
    }
}
