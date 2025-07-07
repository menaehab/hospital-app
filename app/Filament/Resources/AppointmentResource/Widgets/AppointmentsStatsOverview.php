<?php

namespace App\Filament\Resources\AppointmentResource\Widgets;

use App\Models\User;
use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\AppointmentResource\Pages\ListAppointments;

class AppointmentsStatsOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListAppointments::class;
    }

    protected function getStats(): array
    {
        $stats = [];
        $totalAppointments = $this->getPageTableQuery()->count();
        $waitingAppointments = $this->getPageTableQuery()->where('status', 'pending')->count();
        $completedAppointments = $this->getPageTableQuery()->where('status', 'finished')->count();

        if($totalAppointments > 0) {
            $stats[] = Stat::make(__('keywords.total_appointments'), $totalAppointments);
        }
        if($waitingAppointments > 0) {
            $stats[] = Stat::make(__('keywords.waiting_appointments'), $waitingAppointments);
        }
        if($completedAppointments > 0) {
            $stats[] = Stat::make(__('keywords.completed_appointments'), $completedAppointments);
        }

        return $stats;
    }
}