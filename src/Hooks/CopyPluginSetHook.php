<?php


namespace Payone\Events;


use Payone\Models\Settings;
use Payone\Services\SettingsService;
use Plenty\Modules\Plugin\Events\CopyPluginSet;

class CopyPluginSetHook
{
    public function handle(CopyPluginSet $copyPluginSet)
    {
        /** @var SettingsService $settingService */
        $settingService = pluginApp(SettingsService::class);
        $availableSettings = $settingService->getAllSettingsForPluginSetId($copyPluginSet->getSourcePluginSetId());

        if(is_array($availableSettings)) {
            foreach ($availableSettings as $setting) {
                if($setting instanceof Settings) {
                    $setting->id = null;
                    $setting->pluginSetId = $copyPluginSet->getTargetPluginSetId();
                    $setting->save();
                }
            }
        }
    }
}