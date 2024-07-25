<?php

namespace Mrdth\LaravelModelSettings\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasSettings
{
    private string $settings_column;

    // This method is called when the trait is booted, via some Laravel magic.
    public function initializeHasSettings(): void
    {
        $this->settings_column = config('model-settings.column', 'settings');
        $this->checkSettingsExist();

        $this->mergeFillable([$this->settings_column]);
        $this->mergeCasts([$this->settings_column => 'array']);
    }

    public function hasSetting($name): bool
    {
        return isset($this->{$this->settings_column}[$name]);
    }

    public function getSettings(): array
    {
        return $this->{$this->settings_column};
    }

    public function deleteSettings(): void
    {
        $this->{$this->settings_column} = null;
        $this->save();
    }

    public function getSetting(string $name)
    {
        return $this->{$this->settings_column}[$name] ?? null;
    }

    public function addSetting(string $name, $value): void
    {
        throw_if(
            $this->getSetting($name) !== null,
            new \Exception("Setting '$name' already exists")
        );

        $this->updateSetting($name, $value);
    }

    public function updateSetting(string $name, $value): void
    {
        $settings = $this->{$this->settings_column};
        $settings[$name] = $value;
        $this->{$this->settings_column} = $settings;
        $this->save();
    }

    public function deleteSetting(string $name): void
    {
        $settings = $this->{$this->settings_column};
        unset($settings[$name]);
        $this->{$this->settings_column} = $settings;
        $this->save();
    }

    private function checkSettingsExist(): void
    {
        throw_unless(
            Schema::hasColumn($this->getTable(), $this->settings_column),
            new \Exception("Table '{$this->getTable()}' does not have a '{$this->settings_column}' column")
        );

        throw_unless(
            in_array(Schema::getColumnType($this->getTable(), $this->settings_column), ['json', 'text']),
            new \Exception("'{$this->settings_column}' column is not json type")
        );
    }
}
