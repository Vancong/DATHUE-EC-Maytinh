<?php

namespace Tests\Unit;

use App\Models\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_order_status_check_methods(): void
    {
        $statuses = [
            'pending' => 'isPending',
            'confirmed' => 'isConfirmed',
            'processing' => 'isProcessing',
            'shipping' => 'isShipping',
            'delivered' => 'isDelivered',
            'cancelled' => 'isCancelled',
            'failed_delivery' => 'isFailedDelivery',
        ];

        foreach ($statuses as $status => $method) {
            $order = new Order(['order_status' => $status]);
            $this->assertTrue($order->$method(), "Order with status {$status} should return true for {$method}()");

            // Verify another status returns false
            $otherOrder = new Order(['order_status' => 'other_status']);
            $this->assertFalse($otherOrder->$method(), "Order with status other_status should return false for {$method}()");
        }
    }

    public function test_can_be_cancelled_logic(): void
    {
        // Allowed statuses for cancellation (by system / admin)
        $cancellableStatuses = ['pending', 'confirmed', 'processing', 'shipping', 'failed_delivery'];
        foreach ($cancellableStatuses as $status) {
            $order = new Order(['order_status' => $status]);
            $this->assertTrue($order->canBeCancelled(), "Status {$status} should be cancellable.");
        }

        // Terminal statuses cannot be cancelled again
        $nonCancellableStatuses = ['delivered', 'cancelled'];
        foreach ($nonCancellableStatuses as $status) {
            $order = new Order(['order_status' => $status]);
            $this->assertFalse($order->canBeCancelled(), "Status {$status} must not be cancellable.");
        }
    }

    public function test_can_be_cancelled_by_customer_permission_boundary(): void
    {
        // Customer can only cancel before order processing begins
        $allowedForCustomer = ['pending', 'confirmed'];
        foreach ($allowedForCustomer as $status) {
            $order = new Order(['order_status' => $status]);
            $this->assertTrue(
                $order->canBeCancelledByCustomer(),
                "Customer should be allowed to cancel order in {$status} state."
            );
        }

        // Disallowed for customer (processing, shipping, delivered, cancelled, failed_delivery)
        $disallowedForCustomer = ['processing', 'shipping', 'delivered', 'cancelled', 'failed_delivery'];
        foreach ($disallowedForCustomer as $status) {
            $order = new Order(['order_status' => $status]);
            $this->assertFalse(
                $order->canBeCancelledByCustomer(),
                "Customer must NOT be allowed to cancel order in {$status} state."
            );
        }
    }

    public function test_can_be_restocked_conditions(): void
    {
        // Case 1: Cancelled and inventory not yet settled -> eligible for restocking
        $cancelledUnsettled = new Order([
            'order_status' => 'cancelled',
            'inventory_settled' => false,
        ]);
        $this->assertTrue($cancelledUnsettled->canBeRestocked());

        // Case 2: Failed delivery and inventory not yet settled -> eligible for restocking
        $failedDeliveryUnsettled = new Order([
            'order_status' => 'failed_delivery',
            'inventory_settled' => false,
        ]);
        $this->assertTrue($failedDeliveryUnsettled->canBeRestocked());

        // Case 3: Cancelled but inventory already settled -> cannot restock again
        $cancelledSettled = new Order([
            'order_status' => 'cancelled',
            'inventory_settled' => true,
        ]);
        $this->assertFalse($cancelledSettled->canBeRestocked());

        // Case 4: Failed delivery but inventory already settled -> cannot restock again
        $failedDeliverySettled = new Order([
            'order_status' => 'failed_delivery',
            'inventory_settled' => true,
        ]);
        $this->assertFalse($failedDeliverySettled->canBeRestocked());

        // Case 5: Non-restockable statuses even if unsettled (pending, confirmed, processing, shipping, delivered)
        $activeStatuses = ['pending', 'confirmed', 'processing', 'shipping', 'delivered'];
        foreach ($activeStatuses as $status) {
            $order = new Order([
                'order_status' => $status,
                'inventory_settled' => false,
            ]);
            $this->assertFalse(
                $order->canBeRestocked(),
                "Active status {$status} must not be eligible for restocking."
            );
        }
    }
}
