<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mrdth\LaravelModelSettings\Tests\Models\ModelWithoutSettings;
use Mrdth\LaravelModelSettings\Tests\Models\ModelWithSettings;

beforeEach(function () {
    Schema::create(
        'model-with-settings',
        function (Blueprint $table) {
            $table->increments('id');
            $table->json('settings')->nullable();
            $table->timestamps();
        }
    );
});

afterEach(function () {
    Schema::dropIfExists('model-with-settings');
});

test('it initializes settings correctly', function () {
    $model = new ModelWithSettings;

    // Check if settings is added to $fillable.
    expect($model->getFillable())->toContain('settings');

    // Check if settings is cast to array.
    $casts = $model->getCasts();
    expect($casts)->toHaveKey('settings')
        ->and($casts['settings'])->toEqual('array');
});

test('it checks model database table has settings column', function () {
    Schema::create(
        'model-without-settings',
        function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        }
    );

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Table 'model-without-settings' does not have a 'settings' column");

    $model = new ModelWithoutSettings;
});

test('it checks model database table settings column is json type', function () {
    Schema::create(
        'model-without-settings',
        function (Blueprint $table) {
            $table->increments('id');
            $table->string('settings')->nullable();
            $table->timestamps();
        }
    );

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Settings column is not json type');

    $model = new ModelWithoutSettings;
});

test('it can get setting', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar'];

    expect($model->getSetting('foo'))->toEqual('bar');
});

test('it can add settings', function () {
    $model = new ModelWithSettings;

    $model->addSetting('foo', 'bar');

    expect($model->getSetting('foo'))->toEqual('bar');
});

test('it can update settings', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar'];

    $model->updateSetting('foo', 'baz');

    expect($model->getSetting('foo'))->toEqual('baz');
});

test('it can delete settings', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar'];

    $model->deleteSetting('foo');

    expect($model->getSetting('foo'))->toBeNull();
});

test('it can delete all settings', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar', 'baz' => 'qux'];

    $model->deleteSettings();

    expect($model->getSetting('foo'))->toBeNull()
        ->and($model->getSetting('baz'))->toBeNull();
});

test('it throws exception when adding setting which already exists', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar'];

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Setting 'foo' already exists");
    $model->addSetting('foo', 'baz');
});

test('it can get all settings', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar', 'baz' => 'qux'];

    expect($model->getSettings())->toEqual(['foo' => 'bar', 'baz' => 'qux']);
});

test('it can check if setting exists', function () {
    $model = new ModelWithSettings;
    $model->settings = ['foo' => 'bar'];

    expect($model->hasSetting('foo'))->toBeTrue()
        ->and($model->hasSetting('baz'))->toBeFalse();
});
