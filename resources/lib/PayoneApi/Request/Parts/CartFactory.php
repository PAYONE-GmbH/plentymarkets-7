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
        $cart->add(self::calculateShipping($requestData, $cart));
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
            CartItem::TYPE_SHIPMENt,
            1,
            $basket['shippingAmount'],
            $taxRate,
            'Porto & Versand'
        );
        return $shippingCost;
    }
}
