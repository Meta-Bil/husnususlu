<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AppointmentStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Randevu talepleri';

    protected function getStats(): array
    {
        $new = AppointmentRequest::query()->where('status', AppointmentStatus::New)->count();
        $thisWeek = AppointmentRequest::query()->where('created_at', '>=', now()->startOfWeek())->count();
        $thisMonth = AppointmentRequest::query()->where('created_at', '>=', now()->startOfMonth())->count();

        return [
            Stat::make('Yeni talepler', $new)
                ->description('Henüz ele alınmadı')
                ->color($new > 0 ? 'warning' : 'gray'),
            Stat::make('Bu hafta', $thisWeek)
                ->description(now()->startOfWeek()->format('d.m.Y').' tarihinden beri')
                ->color('info'),
            Stat::make('Bu ay', $thisMonth)
                ->description(now()->startOfMonth()->format('F Y'))
                ->color('primary'),
        ];
    }
}
