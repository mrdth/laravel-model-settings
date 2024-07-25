<?php

namespace Mrdth\LaravelModelSettings\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * @mixin \Illuminate\Database\Eloquent\Model

 */
trait HasSettings
{
    // This method is called when the trait is booted, via some Laravel magic.
    public function initializeHasSettings(): void
    {
        $this->checkSettingsExist();

        $this->mergeFillable(['settings']);
        $this->mergeCasts(['settings' => 'array']);
    }

    public function hasSetting($name): bool
    {
        return isset($this->settings[$name]);
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function deleteSettings(): void
    {
        $this->settings = null;
        $this->save();
    }

    public function getSetting(string $name)
    {
        return $this->settings[$name] ?? null;
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
        $settings = $this->settings;
        $settings[$name] = $value;
        $this->settings = $settings;
        $this->save();
    }

    public function deleteSetting(string $name): void
    {
        $settings = $this->settings;
        unset($settings[$name]);
        $this->settings = $settings;
        $this->save();
    }


    private function checkSettingsExist(): void
    {
        throw_unless(
            Schema::hasColumn($this->getTable(), 'settings'),
            new \Exception("Table '{$this->getTable()}' does not have a 'settings' column")
        );

        throw_unless(
            in_array(Schema::getColumnType($this->getTable(), 'settings'), ['json', 'text']),
            new \Exception('Settings column is not json type')
        );
    }
}
