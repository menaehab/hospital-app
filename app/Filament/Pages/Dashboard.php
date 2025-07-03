<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class Dashboard extends Page
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-home';

    /**
     * Get the columns that should be used in the dashboard layout.
     *
     * @return int | array<int, int | null> | null
     */
    protected function getColumns(): int | array | null
    {
        return 2;
    }

    /**
     * Get the widgets that should be visible on the dashboard.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    protected function getVisibleWidgets(): array
    {
        return [
            // Add your widget classes here if needed
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('keywords.dashboard');
    }

    public function getTitle(): string
    {
        return __('keywords.hello') . ' ' . auth()->user()->name;
    }
}
