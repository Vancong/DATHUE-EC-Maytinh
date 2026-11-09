<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_is_admin_check(): void
    {
        $admin = new User(['role' => 'admin']);
        $this->assertTrue($admin->isAdmin());

        $customer = new User(['role' => 'customer']);
        $this->assertFalse($customer->isAdmin());

        $other = new User(['role' => 'staff']);
        $this->assertFalse($other->isAdmin());

        $emptyRole = new User;
        $this->assertFalse($emptyRole->isAdmin());
    }

    public function test_is_customer_check(): void
    {
        $customer = new User(['role' => 'customer']);
        $this->assertTrue($customer->isCustomer());

        $admin = new User(['role' => 'admin']);
        $this->assertFalse($admin->isCustomer());

        $other = new User(['role' => 'guest']);
        $this->assertFalse($other->isCustomer());

        $emptyRole = new User;
        $this->assertFalse($emptyRole->isCustomer());
    }

    public function test_user_hidden_attributes_in_serialization(): void
    {
        $user = new User([
            'name' => 'Nguyen Van A',
            'email' => 'vana@example.com',
            'password' => 'secret_password_hash',
            'remember_token' => 'sample_token',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
    }
}
