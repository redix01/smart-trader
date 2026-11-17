<?php

namespace Tests\Feature\Admin;

/** Admin UI shell: mobile card tables, one flash message per page, no stray third-party links. */
class AdminLayoutTest extends AdminTestCase
{
    public function test_layout_ships_the_mobile_table_cards_and_has_no_stray_links(): void
    {
        $html = $this->asAdmin()->get(route('admin.user.index'))->assertOk()->getContent();

        $this->assertStringContainsString('table:not([data-keep-table])', $html); // table -> card CSS
        $this->assertStringContainsString("setAttribute('data-label'", $html);     // header text copied to each cell
        $this->assertStringNotContainsString('flowbite.com', $html);
        $this->assertDoesNotMatchRegularExpression('/\*\s*\{\s*transition:\s*all/', $html);
    }

    public function test_a_flash_message_is_shown_once_not_twice(): void
    {
        $user = $this->member();
        $message = 'Order cancelled and unique-flash-text';

        foreach (['admin.transactions.deposits', 'admin.trade.history', 'admin.live-trades.index', 'admin.plans.index', 'admin.signals.index'] as $route) {
            $html = $this->asAdmin()->withSession(['success' => $message])->get(route($route))->assertOk()->getContent();
            $this->assertSame(1, substr_count($html, $message), "$route shows the flash message more than once");
        }
    }
}
