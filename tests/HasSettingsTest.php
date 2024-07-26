<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mrdth\LaravelModelSettings\Exceptions\InvalidColumnTypeException;
use Mrdth\LaravelModelSettings\Exceptions\MissingSettingsColumnException;
use Mrdth\LaravelModelSettings\Tests\Models\ModelWithoutSettings;
use Mrdth\LaravelModelSettings\Tests\Models\ModelWithSettings;

beforeEach(function () {
    $settings_column = config('model-settings.column');

    Schema::create(
        'model-with-settings',
        function (Blueprint $table) use ($settings_column) {
            $table->increments('id');
            $table->json($settings_column)->nullable();
            $table->timestamps();
        }
    );
});

afterEach(function () {
    Schema::dropIfExists('model-with-settings');
    Schema::dropIfExists('model-without-settings');
});

it('initializes settings correctly', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;

    // Check if settings is added to $fillable.
    expect($model->getFillable())->toContain($settings_column);

    // Check if settings is cast to array.
    $casts = $model->getCasts();
    expect($casts)->toHaveKey($settings_column)
        ->and($casts[$settings_column])->toEqual('array');
});

it('checks model database table has settings column', function () {
    $settings_column = config('model-settings.column');
    Schema::create(
        'model-without-settings',
        function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        }
    );

    $this->expectException(MissingSettingsColumnException::class);
    $this->expectExceptionMessage("Table 'model-without-settings' does not have a '$settings_column' column");

    $model = new ModelWithoutSettings;
    $model->getSetting('foo');
});

it('checks model database table settings column is json type', function () {
    $settings_column = config('model-settings.column');
    Schema::create(
        'model-without-settings',
        function (Blueprint $table) use ($settings_column) {
            $table->increments('id');
            $table->string($settings_column)->nullable();
            $table->timestamps();
        }
    );

    $this->expectException(InvalidColumnTypeException::class);
    $this->expectExceptionMessage("'$settings_column' column is not json type");

    $model = new ModelWithoutSettings;
    $model->getSetting('foo');
});

it('can get setting', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar'];

    expect($model->getSetting('foo'))->toEqual('bar');
});

it('can get setting with default value', function () {
    $model = new ModelWithSettings;

    expect($model->getSetting('foo', 'bar'))->toEqual('bar');
});

it('can add settings', function () {
    $model = new ModelWithSettings;

    $model->addSetting('foo', 'bar');

    expect($model->getSetting('foo'))->toEqual('bar');
});

it('can update settings', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar'];

    $model->updateSetting('foo', 'baz');

    expect($model->getSetting('foo'))->toEqual('baz');
});

it('can delete settings', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar'];

    $model->deleteSetting('foo');

    expect($model->getSetting('foo'))->toBeNull();
});

it('can delete all settings', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar', 'baz' => 'qux'];

    $model->deleteSettings();

    expect($model->getSetting('foo'))->toBeNull()
        ->and($model->getSetting('baz'))->toBeNull();
});

it('throws exception when adding setting which already exists', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar'];

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Setting 'foo' already exists");
    $model->addSetting('foo', 'baz');
});

it('can get all settings', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar', 'baz' => 'qux'];

    expect($model->getSettings())->toEqual(['foo' => 'bar', 'baz' => 'qux']);
});

it('can check if setting exists', function () {
    $settings_column = config('model-settings.column');
    $model = new ModelWithSettings;
    $model->$settings_column = ['foo' => 'bar'];

    expect($model->hasSetting('foo'))->toBeTrue()
        ->and($model->hasSetting('baz'))->toBeFalse();
});
