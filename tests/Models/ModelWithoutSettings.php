<?php

namespace Mrdth\LaravelModelSettings\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Mrdth\LaravelModelSettings\Concerns\HasSettings;

class ModelWithoutSettings extends Model
{
    use HasSettings;

    protected $table = 'model-without-settings';
}
