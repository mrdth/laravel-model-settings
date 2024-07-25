<?php

namespace Mrdth\LaravelModelSettings\Services;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelFinderService
{
    public function getModel($model): ?string
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                $class = sprintf(
                    "\%s%s",
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );

                return $class;
            })
            ->filter(function ($class) use ($model) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    $valid =
                        $reflection->isSubclassOf(Model::class) &&
                        ! $reflection->isAbstract() &&
                        Str::of($class)->endsWith("\\$model");
                }

                return $valid;
            });

        return $models->first();
    }
}
