<?php

namespace LaravelFly\Dict\Bootstrap;

use Illuminate\Foundation\PackageManifest;
use LaravelFly\Dict\Application;

class ReadProvidersConfig
{

    public function bootstrap(Application $app)
    {

        $appConfig = $app->make('config');

        $psInRequest = $appConfig['laravelfly.providers_in_request'];

        $worker_providers = $app['config']['laravelfly.providers_on_worker'];
        $CFServices = [];
        $providersReplaced = [];
        $providersOnWork = [];
        foreach ($worker_providers as $provider => $providerConfig) {

            if ($providerConfig === false || is_null($providerConfig)) continue;

            $providersOnWork[] = $provider;

            if (!is_array($providerConfig)) continue;

            if (isset($providerConfig['_replace'])) {
                $providersReplaced[] = $providerConfig['_replace'];
                unset($providerConfig['_replace']);
            }

            $officalCFServices = $provider::coroutineFriendlyServices();
            // $officalCFServices is base
            $curCFServices = $officalCFServices;

            foreach ($providerConfig as $CFS_name => $config) {
                // true $config only works when empty $officalCFServices
                if ($config === true && !$officalCFServices) {
                    $curCFServices[] = $CFS_name;
                } elseif ($config === false && in_array($CFS_name, $officalCFServices)) {
                    $curCFServices = array_diff($curCFServices, [$CFS_name]);
                }
            }


            if ($curCFServices) {
                $CFServices = array_unique(array_merge($curCFServices, $CFServices));
            }
        }

        $providersInComposer = $app->make(PackageManifest::class)->providers();
        $allProviders = array_merge($appConfig['app.providers'], $providersInComposer);

        // 'app.providers' only providers across
        $appConfig['app.providers'] = array_diff(
            $allProviders,
            $providersReplaced,
            $providersOnWork,
            $psInRequest,
            $appConfig['laravelfly.providers_ignore']
        );

        $app->makeManifestForProvidersInRequest($psInRequest);

        $app->setProvidersToBootOnWorker($providersOnWork);

        $app->setCFServices($CFServices);

    }
}