<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $secondAdmin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('secretadmin1'),
            'is_active' => true,
        ]);

        $this->secondAdmin = User::create([
            'name' => 'Second Admin',
            'email' => 'secondadmin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('secretadmin2'),
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@techstore.vn',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_admin_accounts_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.accounts.index'));
        $response->assertStatus(200);
        $response->assertSee('Super Admin');
        $response->assertSee('Second Admin');
    }

    public function test_admin_can_create_new_admin_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.accounts.store'), [
            'name' => 'Third Admin Operator',
            'email' => 'thirdadmin@techstore.vn',
            'phone' => '0933889900',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'thirdadmin@techstore.vn',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $newAdmin = User::where('email', 'thirdadmin@techstore.vn')->first();
        $this->assertTrue(Hash::check('newpassword123', $newAdmin->password));
    }

    public function test_admin_cannot_create_account_with_duplicate_email(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.accounts.store'), [
            'name' => 'Duplicate Admin',
            'email' => 'superadmin@techstore.vn', // Already exists
            'password' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_toggle_other_admin_active_status(): void
    {
        $this->assertTrue($this->secondAdmin->is_active);

        // 1. Lock second admin
        $lockResponse = $this->actingAs($this->superAdmin)->post(
            route('admin.accounts.toggle-active', $this->secondAdmin->id)
        );
        $lockResponse->assertSessionHas('success');

        $this->secondAdmin->refresh();
        $this->assertFalse($this->secondAdmin->is_active);

        // 2. Unlock second admin
        $unlockResponse = $this->actingAs($this->superAdmin)->post(
            route('admin.accounts.toggle-active', $this->secondAdmin->id)
        );
        $unlockResponse->assertSessionHas('success');

        $this->secondAdmin->refresh();
        $this->assertTrue($this->secondAdmin->is_active);
    }

    public function test_admin_cannot_toggle_self_active_status(): void
    {
        $this->assertTrue($this->superAdmin->is_active);

        // Super Admin attempts to lock themselves
        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.accounts.toggle-active', $this->superAdmin->id)
        );

        $response->assertSessionHas('error', 'Bạn không thể tự khóa tài khoản của chính mình.');

        $this->superAdmin->refresh();
        $this->assertTrue($this->superAdmin->is_active, 'Super admin account must remain active.');
    }

    public function test_admin_cannot_toggle_non_admin_account_via_admin_account_route(): void
    {
        // Customer ID passed to admin accounts toggle
        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.accounts.toggle-active', $this->customer->id)
        );

        $response->assertStatus(404); // abort_if($admin->role !== 'admin', 404)
    }

    public function test_customer_or_guest_cannot_access_admin_accounts(): void
    {
        // Guest
        $guestResp = $this->get(route('admin.accounts.index'));
        $guestResp->assertRedirect(route('admin.login'));

        // Customer
        $custResp = $this->actingAs($this->customer)->get(route('admin.accounts.index'));
        $custResp->assertRedirect(route('admin.login'));
    }
}

