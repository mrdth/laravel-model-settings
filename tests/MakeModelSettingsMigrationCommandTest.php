<?php

use Mrdth\LaravelModelSettings\Commands\MakeModelSettingsMigrationCommand;
use Mrdth\LaravelModelSettings\Services\ModelFinderService;

it('runs successfully', function () {
    $this->withoutExceptionHandling();

    $mock = Mockery::mock(ModelFinderService::class)
        ->shouldReceive('getModel')
        ->with('ModelWithSettings')
        ->andReturn('\Mrdth\LaravelModelSettings\Tests\Models\ModelWithSettings');

    $this->artisan(MakeModelSettingsMigrationCommand::class, ['name' => 'ModelWithSettings'])
        ->assertSuccessful();

    dd($mock);
});

it('creates the migration when called', function () {
    $this->artisan(MakeModelSettingsMigrationCommand::class, ['name' => 'ModelWithSettings'])
        ->assertExitCode(0);

    $this->assertFileExists(database_path('migrations'));

});
