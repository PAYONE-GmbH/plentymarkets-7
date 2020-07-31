<?php

namespace Payone\Helpers;

use Payone\PluginConstants;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Comment\Contracts\CommentRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Property\Models\OrderProperty;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Log\Loggable;

class OrderHelper
{
    use Loggable;
    /**
     * @param Order $order
     *
     * @return string
     */
    public function getLang(Order $order)
    {
        /** @var OrderProperty $property */
        foreach ($order->properties as $property) {
            if ($property->typeId == OrderPropertyType::DOCUMENT_LANGUAGE) {
                return $property->value;
            }
        }

        return 'DE';
    }

    /**
     * Adds a note to an order
     *
     * @param $refValue
     * @param $msg
     * @param null $backendUserId
     * @throws \Throwable
     *
     */
    public function addOrderComment($refValue, $msg)
    {
        /** @var ConfigRepository $configRepository */
        $configRepository = pluginApp(ConfigRepository::class);
        $backendUserId = $configRepository->get(PluginConstants::NAME . '.userId', null);

        if (isset($backendUserId))
        {
            $commentData = [];
            $commentData['referenceType'] = 'order';
            $commentData['referenceValue'] = $refValue;
            $commentData['text'] = $msg;
            $commentData['isVisibleForContact'] = false;
            $commentData['userId'] = (int) $backendUserId;

            try
            {
                /** @var  AuthHelper $authHelper */
                $authHelper = pluginApp(AuthHelper::class);

                $authHelper->processUnguarded(
                    function () use ($commentData) {
                        /** @var CommentRepositoryContract $commentRepo */
                        $commentRepo = pluginApp(CommentRepositoryContract::class);
                        //unguarded
                        $commentRepo->createComment($commentData);
                    }
                );
            } catch (\Exception $e) {}
        }
    }
}
