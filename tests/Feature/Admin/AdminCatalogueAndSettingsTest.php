<?php

namespace Tests\Feature\Admin;

use App\Models\CopyTrader;
use App\Models\PaymentMethod;
use App\Models\TradePair;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/** Admin-managed catalogues (payment methods, pairs, copy traders) and settings: what the admin saves is what users see. */
class AdminCatalogueAndSettingsTest extends AdminTestCase
{
    public function test_payment_method_added_by_admin_shows_on_the_user_deposit_page(): void
    {
        $this->asAdmin()->post(route('admin.payment-method.store'), ['crypto_type' => 'usdt_trc20', 'address' => 'TXyzAdminWallet123'])->assertSessionHas('success');
        $pm = PaymentMethod::first();
        $this->assertTrue((bool) $pm->is_active);

        $this->actingAs($this->member())->get(route('user.deposit'))->assertOk()->assertSee('TXyzAdminWallet123');

        $this->asAdmin()->put(route('admin.payment-method.update', $pm->id), ['crypto_type' => 'bitcoin', 'address' => 'bc1qnewaddress'])->assertSessionHas('success');
        $this->actingAs($this->member())->get(route('user.deposit'))->assertSee('bc1qnewaddress')->assertDontSee('TXyzAdminWallet123');

        $this->asAdmin()->get(route('admin.payment-method.index'))->assertOk()->assertSee('bc1qnewaddress');
        $this->asAdmin()->delete(route('admin.payment-method.destroy', $pm->id))->assertSessionHas('success');
        $this->assertSame(0, PaymentMethod::count());
    }

    public function test_trade_pair_crud(): void
    {
        $this->asAdmin()->post(route('admin.trade-pair.store'), ['type' => 'crypto', 'pair' => 'ETH/USD'])->assertSessionHas('success');
        $pair = TradePair::first();

        $this->asAdmin()->get(route('admin.trade-pair.index'))->assertOk()->assertSee('ETH/USD');
        $this->asAdmin()->put(route('admin.trade-pair.update', $pair->id), ['type' => 'crypto', 'pair' => 'SOL/USD'])->assertSessionHas('success');
        $this->assertSame('SOL/USD', $pair->fresh()->pair);
        $this->asAdmin()->delete(route('admin.trade-pair.destroy', $pair->id))->assertSessionHas('success');
        $this->assertSame(0, TradePair::count());
    }

    public function test_copy_trader_created_by_admin_is_listed_for_users(): void
    {
        $payload = ['name' => 'Whale One', 'amount' => 500, 'win_rate' => '88', 'profit_share' => '15', 'win' => '40', 'loss' => '5'];
        $this->asAdmin()->post(route('admin.copy-trader.store'), $payload)->assertSessionHas('success');
        $trader = CopyTrader::first();

        $this->asAdmin()->get(route('admin.copy-trader.index'))->assertOk()->assertSee('Whale One');
        $this->actingAs($this->member())->get(route('user.copyTrading.index'))->assertOk()->assertSee('Whale One');

        $this->asAdmin()->put(route('admin.copy-trader.update', $trader->id), ['name' => 'Whale Two'] + $payload)->assertSessionHas('success');
        $this->asAdmin()->delete(route('admin.copy-trader.destroy', $trader->id))->assertSessionHas('success');
        $this->assertSame(0, CopyTrader::count());
    }

    public function test_livechat_settings_saved_by_admin_reach_the_user_support_page(): void
    {
        Storage::fake('local');

        $this->asAdmin()->post(route('admin.settings.livechat.update'), [
            'provider' => 'jivochat', 'widget_script' => '<script src="//code.jivosite.com/widget/AdminSavedId" async></script>',
            'is_enabled' => 1, 'show_on_support_page' => 1, 'widget_position' => 'bottom-right',
        ])->assertSessionHas('success');

        $this->actingAs($this->member())->get('/user/support')->assertOk()->assertSee('AdminSavedId');
    }

    public function test_settings_pages_save(): void
    {
        Storage::fake('local');

        $this->asAdmin()->get(route('admin.settings.index'))->assertOk();
        $this->asAdmin()->post(route('admin.settings.website.update'), ['site_name' => 'Prime Digital Capital', 'primary_color' => '#112233', 'secondary_color' => '#445566'])->assertSessionHas('success');
        Storage::disk('local')->assertExists('website_settings.json');
        $this->asAdmin()->get(route('admin.security'))->assertOk();
    }

    public function test_admin_can_change_their_own_password_only_with_the_current_one(): void
    {
        $this->asAdmin()->post(route('admin.resetPassword'), ['current_password' => 'wrong', 'new_password' => 'brand-new-pass-1', 'new_password_confirmation' => 'brand-new-pass-1'])->assertSessionHasErrors();
        $this->asAdmin()->post(route('admin.resetPassword'), ['current_password' => 'password', 'new_password' => 'brand-new-pass-1', 'new_password_confirmation' => 'brand-new-pass-1'])->assertSessionHas('success');
        $this->assertTrue(\Hash::check('brand-new-pass-1', $this->admin->fresh()->password));
    }

    public function test_email_broadcast_page_and_send(): void
    {
        Mail::fake();
        $user = $this->member();

        $this->asAdmin()->get(route('admin.notifications.index'))->assertOk()->assertSee($user->email);
        $this->asAdmin()->post(route('admin.notifications.send'), [
            'recipient_source' => 'users', 'user_ids' => [$user->id], 'from_identity' => 'admin',
            'subject' => 'Maintenance', 'message' => 'We will be back soon.', 'header_color' => '#112233',
        ]);
        Mail::assertSentCount(1);
        $this->asAdmin()->get(route('admin.notifications.index'))->assertOk()->assertSee('Maintenance');
    }
}
