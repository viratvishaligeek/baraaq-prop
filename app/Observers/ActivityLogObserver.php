<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLogObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created');
    }

    public function updated(Model $model): void
    {
        $changed = array_values(array_diff(array_keys($model->getChanges()), ['updated_at']));

        if ($changed !== []) {
            $this->record($model, 'updated', $changed);
        }
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted');
    }

    private function record(Model $model, string $event, array $changed = []): void
    {
        $label = data_get($model, 'title')
            ?: data_get($model, 'name')
            ?: data_get($model, 'email')
            ?: 'ID '.$model->getKey();

        ActivityLog::create([
            'causer_id' => auth()->id(),
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'event' => $event,
            'description' => Str::headline(class_basename($model)).' "'.$label.'" '.$event,
            'properties' => $changed === [] ? null : ['changed_fields' => $changed],
            'created_at' => now(),
        ]);
    }
}
