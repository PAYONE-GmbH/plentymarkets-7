<?php

namespace Payone\Procedures;

use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderType;

/**
 * Class AbstractEventProcedure
 */
abstract class AbstractEventProcedure
{
    protected $orderRepo;

    /**
     * AbstractEventProcedure constructor.
     *
     * @param OrderRepositoryContract $orderRepo
     */
    public function __construct(OrderRepositoryContract $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    /**
     * @param Order $order
     *
     * @return Order
     */
    protected function getOriginalOrder(Order $order)
    {
        switch ($order->typeId) {
            case OrderType::TYPE_SALES_ORDER:
                return $order;

            default:
                return $this->orderRepo->findOrderById($order->originOrder);
        }
    }
}
