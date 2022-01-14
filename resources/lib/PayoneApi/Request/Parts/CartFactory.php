<?php


namespace PayoneApi\Request\Parts;


class CartFactory
{
    /**
     * @param array $requestData
     * @return Cart
     */
    static public function create(array $requestData)
    {
        $cart = new Cart();
        foreach ($requestData['basketItems'] as $i => $cartItemData) {
            if(isset($cartItemData['itemId'])) {
                $cartItem = new CartItem(
                    ($i+1),
                    $cartItemData['itemId'],
                    CartItem::TYPE_GOODS,
                    $cartItemData['quantity'] ?? '',
                    $cartItemData['price'],
                    $cartItemData['vat'],
                    $cartItemData['name'] ?? ''
                );
                $cart->add($cartItem);
            }
        }
        $cart->add(self::calculateShipping($requestData, $cart));
        if(isset($requestData['basket']['couponDiscount'])) {
            $cart->add(self::calculateVoucher($requestData, $cart));
        }
        return $cart;
    }

    /**
     * @param array $requestData
     * @param $cart
     * @return CartItem
     */
    private static function calculateShipping(array $requestData, Cart $cart)
    {
        $taxRate = 0;
        $basket = $requestData['basket'];
        if ($basket['shippingAmountNet'] > 0) {
            $taxRate = (int)round((($basket['shippingAmount'] / $basket['shippingAmountNet']) - 1) * 100);
        }
        $shippingCost = new CartItem(
            (count($cart->getCartItems())+1),
            '-',
            CartItem::TYPE_SHIPMENT,
            1,
            $basket['shippingAmount'],
            $taxRate,
            'Porto & Versand'
        );
        return $shippingCost;
    }
    /**
     * @param array $requestData
     * @param $cart
     * @return CartItem
     */
    private static function calculateVoucher(array $requestData, Cart $cart)
    {
        $basket = $requestData['basket'];
        $voucherValue = $basket['couponDiscount'] * 100;
        $voucher = new CartItem(
            (count($cart->getCartItems()) + 2),
            '-',
            CartItem::TYPE_VOUCHER,
            1,
            $voucherValue,
            '19',
            '-'
        );

        return $voucher;
    }
}
