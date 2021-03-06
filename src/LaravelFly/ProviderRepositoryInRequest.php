<?php

namespace LaravelFly;


class ProviderRepositoryInRequest extends \Illuminate\Foundation\ProviderRepository
{
    public $manifest;

    /**
     * Override
     */
    public function makeManifest(array $providers)
    {
        $manifest = $this->loadManifest();

        if ($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($providers);
        }

        $this->manifest = $manifest;
    }

    /**
     * Override
     */
    public function load(array $providers)
    {

        $manifest = $this->manifest;

        if (isset($manifest['when'])) {
            foreach ($manifest['when'] as $provider => $events) {
                $this->registerLoadEvents($provider, $events);
            }
        }

        if (isset($manifest['eager'])) {
            foreach ($manifest['eager'] as $provider) {
                $this->app->register($this->createProvider($provider));
            }
        }

        // laravelfly
        // this function sould run more than one time
        // $this->app->setDeferredServices($manifest['deferred']);
        if ($manifest['deferred']) {
            $this->app->addDeferredServices($manifest['deferred']);
        }
    }


}
