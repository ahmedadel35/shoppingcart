<?php

namespace Abo3adel\ShoppingCart\Traits\Base;

use Abo3adel\ShoppingCart\Cart;
use Abo3adel\ShoppingCart\CartItem;

trait UpdatingItemsMethod
{
    public function update(
        int $itemId,
        $qty,
        $opt1 = null,
        $opt2 = null,
        array $options = []
    ): bool {
        // TODO throw exception if item not found

        if (auth()->check()) {
            $item = $this->find($itemId);
            $item = $this->attachValusToItems(
                $item,
                $qty,
                $opt1,
                $opt2,
                $options
            );
            
            return $item->update();
        }

        $items = (collect(session(Cart::sessionName())))
            ->map(function (
                $item
            ) use (
                $itemId,
                $qty,
                $opt1,
                $opt2,
                $options
            ) {
                $item = is_array($item) ? new CartItem($item) : $item;
                if ($item->id === $itemId) {
                    $item = $this->attachValusToItems(
                        $item,
                        $qty,
                        $opt1,
                        $opt2,
                        $options
                    );
                }
                return $item;
            });

        return !!(session([Cart::sessionName() => $items]));
    }

    private function attachValusToItems(
        CartItem $item,
        $qty,
        $opt1 = null,
        $opt2 = null,
        array $options = []
    ): CartItem {
        if (is_int($qty)) {
            $item->qty = $qty;
        } elseif (is_array($qty)) {
            $item->options = $qty;
        }

        if (sizeof($options)) {
            $item->options = $options;
        }

        if (null !== $opt1) {
            if (Cart::fopt()) {
                $item->{Cart::fopt()} = $opt1;

                if (Cart::sopt()) {
                    $item->{Cart::sopt()} = $opt2;
                } else {
                    $item->options = $opt2;
                }
            } else {
                $item->options = $opt1;
            }
        }

        return $item;
    }
}
