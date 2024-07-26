<?php

namespace Mrdth\LaravelModelSettings\Concerns;

use Illuminate\Support\Facades\Schema;
use Mrdth\LaravelModelSettings\Exceptions\InvalidColumnTypeException;
use Mrdth\LaravelModelSettings\Exceptions\MissingSettingsColumnException;

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

        $this->mergeFillable([$this->settings_column]);
        $this->mergeCasts([$this->settings_column => 'array']);
    }

    /**
     * @throws \Throwable
     */
    public function hasSetting($name): bool
    {
        $this->checkSettingsExist();

        return isset($this->{$this->settings_column}[$name]);
    }

    /**
     * @throws \Throwable
     */
    public function getSettings(): array
    {
        $this->checkSettingsExist();

        return $this->{$this->settings_column};
    }

    /**
     * @throws \Throwable
     */
    public function deleteSettings(): void
    {
        $this->checkSettingsExist();
        $this->{$this->settings_column} = null;
        $this->save();
    }

    /**
     * @throws \Throwable
     */
    public function getSetting(string $name, $default = null)
    {
        $this->checkSettingsExist();

        return $this->{$this->settings_column}[$name] ?? $default;
    }

    /**
     * @throws \Throwable
     */
    public function addSetting(string $name, $value): void
    {
        $this->checkSettingsExist();
        throw_if(
            $this->getSetting($name) !== null,
            new \Exception("Setting '$name' already exists")
        );

        $this->updateSetting($name, $value);
    }

    /**
     * @throws \Throwable
     */
    public function updateSetting(string $name, $value): void
    {
        $this->checkSettingsExist();
        $settings = $this->{$this->settings_column};
        $settings[$name] = $value;
        $this->{$this->settings_column} = $settings;
        $this->save();
    }

    /**
     * @throws \Throwable
     */
    public function deleteSetting(string $name): void
    {
        $this->checkSettingsExist();
        $settings = $this->{$this->settings_column};
        unset($settings[$name]);
        $this->{$this->settings_column} = $settings;
        $this->save();
    }

    /**
     * @throws \Throwable If the settings column does not exist or is not a json column
     */
    private function checkSettingsExist(): void
    {
        throw_unless(
            Schema::hasColumn($this->getTable(), $this->settings_column),
            new MissingSettingsColumnException("Table '{$this->getTable()}' does not have a '{$this->settings_column}' column")
        );

        throw_unless(
            in_array(Schema::getColumnType($this->getTable(), $this->settings_column), ['json', 'text']),
            new InvalidColumnTypeException("'{$this->settings_column}' column is not json type")
        );
    }
}
