@extends('admin.layouts.app')

@section('title', 'Homepage content')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Homepage content</h1>
            <p class="text-muted mb-0">Edit the content and images shown on the fixed Homepage 9 design.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View live homepage
        </a>
    </div>

    <div class="content-card mb-4">
        <h2 class="section-title mb-3">Homepage SEO</h2>
        <form method="POST" action="{{ route('admin.homepages.update', $homepage) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label class="form-label" for="meta_title">SEO title</label>
                <input class="form-control" id="meta_title" name="meta_title" maxlength="255"
                    value="{{ old('meta_title', $homepage->meta_title) }}">
            </div>
            <div class="mb-3">
                <label class="form-label" for="meta_description">SEO description</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                    maxlength="1000">{{ old('meta_description', $homepage->meta_description) }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Save SEO details</button>
        </form>
    </div>

    @forelse ($homepage->sections as $section)
        @php
            $definition = $sectionDefinitions[$section->section_key] ?? null;
            $fields = $definition['fields'] ?? [];
            $config = $section->config ?? [];
        @endphp
        @continue(!$definition || empty($fields))

        <div class="content-card mb-4" id="section-{{ $section->id }}">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="section-title mb-1">{{ $definition['label'] ?? str($section->section_key)->replace('.', ' ')->title() }}</h2>
                    <p class="text-muted small mb-0">{{ $section->is_enabled ? 'Visible on homepage' : 'Currently hidden' }}</p>
                </div>
                <span class="badge {{ $section->is_enabled ? 'bg-success' : 'bg-secondary' }}">{{ $section->is_enabled ? 'Enabled' : 'Disabled' }}</span>
            </div>

            <form method="POST" enctype="multipart/form-data"
                action="{{ route('admin.homepages.sections.update', [$homepage, $section]) }}">
                @csrf
                @method('PATCH')
                <div class="row g-3">
                    @foreach ($fields as $key => $field)
                        @php
                            $type = $field['type'] ?? 'text';
                            $value = old('config.' . $key, $config[$key] ?? '');
                            $attachment = $section->mediaAttachments->firstWhere('field_key', $key);
                            $currentImage = $attachment?->media?->url;
                            if (!$currentImage && is_numeric($value)) {
                                $currentImage = \App\Models\Media::query()->find((int) $value)?->url;
                            }
                        @endphp
                        <div class="{{ in_array($type, ['textarea', 'richtext', 'image'], true) ? 'col-12' : 'col-md-6' }}">
                            @if ($type === 'checkbox')
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="section-{{ $section->id }}-{{ $key }}"
                                        name="config[{{ $key }}]" value="1" @checked((bool) $value)>
                                    <label class="form-check-label" for="section-{{ $section->id }}-{{ $key }}">{{ $field['label'] ?? str($key)->replace('_', ' ')->title() }}</label>
                                </div>
                            @else
                                <label class="form-label" for="section-{{ $section->id }}-{{ $key }}">{{ $field['label'] ?? str($key)->replace('_', ' ')->title() }}</label>
                                @if ($type === 'textarea' || $type === 'richtext')
                                    <textarea class="form-control" id="section-{{ $section->id }}-{{ $key }}"
                                        name="config[{{ $key }}]" rows="3" maxlength="{{ $field['max'] ?? 2000 }}">{{ $value }}</textarea>
                                @elseif ($type === 'select')
                                    <select class="form-select" id="section-{{ $section->id }}-{{ $key }}" name="config[{{ $key }}]">
                                        @foreach (($field['options'] ?? []) as $option => $label)
                                            <option value="{{ $option }}" @selected((string) $value === (string) $option)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($type === 'image')
                                    @if ($currentImage)
                                        <div class="mb-2"><img src="{{ $currentImage }}" alt="" style="max-width:180px;max-height:110px;object-fit:contain" class="rounded border p-1"></div>
                                    @endif
                                    <input class="form-control" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                                        id="section-{{ $section->id }}-{{ $key }}" name="config[{{ $key }}]">
                                    <div class="form-text">Upload a replacement image (JPG, PNG, WebP or GIF; up to 10 MB). Leave empty to keep the current image.</div>
                                @else
                                    <input class="form-control" type="{{ in_array($type, ['number', 'url', 'color'], true) ? $type : 'text' }}"
                                        id="section-{{ $section->id }}-{{ $key }}" name="config[{{ $key }}]"
                                        value="{{ $value }}" maxlength="{{ $field['max'] ?? 255 }}"
                                        @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                        @if(isset($field['max']) && $type === 'number') max="{{ $field['max'] }}" @endif>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i> Save section</button>
                </div>
            </form>
        </div>
    @empty
        <div class="alert alert-warning">Homepage 9 has no configured sections. Run the existing homepage content seeder after reviewing the production database backup.</div>
    @endforelse
@endsection
