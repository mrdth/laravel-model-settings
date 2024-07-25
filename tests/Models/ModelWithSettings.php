<?php

namespace Mrdth\LaravelModelSettings\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Mrdth\LaravelModelSettings\Concerns\HasSettings;

class ModelWithSettings extends Model
{
    use HasSettings;

    protected $table = 'model-with-settings';
}
