<?php

use Mrdth\LaravelModelSettings\Commands\MakeModelSettingsMigrationCommand;
use Mrdth\LaravelModelSettings\Services\ModelFinderService;

it('runs successfully', function () {
    $this->withoutExceptionHandling();

    $mock = Mockery::mock(ModelFinderService::class);
    $mock->shouldReceive('getModel')
        ->with('ModelWithSettings')
        ->andReturn('\Mrdth\LaravelModelSettings\Tests\Models\ModelWithSettings');

    dd($mock);

    $this->artisan(MakeModelSettingsMigrationCommand::class, ['name' => 'ModelWithSettings'])
        ->assertSuccessful();

});

it('creates the migration when called', function () {
    $this->artisan(MakeModelSettingsMigrationCommand::class, ['name' => 'ModelWithSettings'])
        ->assertExitCode(0);

    $this->assertFileExists(database_path('migrations'));

});
