<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DialogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_view_uses_browser_alert_or_confirm(): void
    {
        $offenders = [];
        foreach (File::allFiles(resource_path('views')) as $file) {
            if ($file->getFilename() === 'dialogs.blade.php') {
                continue;
            }
            foreach (preg_split('/\R/', $file->getContents()) as $n => $line) {
                if (preg_match('/(^|[^.\w])(alert|confirm)\(/', $line)) {
                    $offenders[] = $file->getRelativePathname().':'.($n + 1);
                }
            }
        }

        $this->assertSame([], $offenders, "Use uiAlert()/uiConfirm() or data-confirm instead of browser dialogs:\n".implode("\n", $offenders));
    }

    public function test_layouts_load_the_modal_helper(): void
    {
        $this->actingAs(User::factory()->create())->get('/user/dashboard')->assertOk()
            ->assertSee('window.uiConfirm = function', false)
            ->assertSee("document.addEventListener('submit'", false);

        foreach (['dashboard/layout/app', 'admin/layouts/app', 'layouts/guest', 'pages/layout/app'] as $layout) {
            $this->assertStringContainsString("@include('partials.dialogs')", file_get_contents(resource_path("views/{$layout}.blade.php")), $layout);
        }
    }
}
