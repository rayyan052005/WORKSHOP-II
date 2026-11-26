<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_are_sent_to_login(): void
    {
        $this->get('/')->assertRedirect('/login.php');
        $this->get('/login.php')->assertOk()->assertSee('Sign in');
    }

    public function test_guest_can_visit_student_pages_but_not_vendor_portal(): void
    {
        $this->post('/login.php', ['action' => 'guest'])->assertRedirect('/welcome.php');
        foreach (['welcome.php', 'index.php', 'favorites.php', 'orders.php'] as $page) {
            $this->get('/'.$page)->assertOk();
        }
        $this->get('/vendor.php')->assertForbidden();
        $this->post('/logout')->assertRedirect('/login.php');
        $this->get('/index.php')->assertRedirect('/login.php');
    }

    public function test_database_login_and_invalid_password(): void
    {
        User::factory()->create(['email' => 'student@example.com', 'password' => Hash::make('secret123')]);
        $this->post('/login.php', ['username' => 'student@example.com', 'password' => 'invalid123'])->assertSessionHasErrors('username');
        $this->post('/login.php', ['username' => 'student@example.com', 'password' => 'secret123'])->assertRedirect('/welcome.php')->assertSessionHas('role', 'Student');
    }

    public function test_seller_can_render_vendor_portal(): void
    {
        $this->withSession(['user_id' => 'seller_1', 'full_name' => 'Test Vendor', 'role' => 'seller'])->get('/vendor.php')->assertOk()->assertSee('Stall & Order Manager', false);
    }
}
