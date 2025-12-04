<?php

namespace App\Filament\Resources\Skills\Widgets;

use App\Models\Skill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SkillStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Skill::notArchived()->count();
        $active = Skill::active()->notArchived()->count();
        $inactive = $total - $active;
        $avgProficiency = Skill::notArchived()
            ->whereNotNull('proficiency_level')
            ->avg('proficiency_level');

        return [
            Stat::make('Total Skills', $total)
                ->description('All non-archived skills')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->chart([
                    // quick funny story (who knows) :)
                    $total  * .1,
                    $total  * .2,
                    $total  * .3,
                    $total  * .4,
                    $total  * .5,
                    $total  * .6,
                    $total  * .7,
                    $total  * .8,
                    $total  * .9,
                    $total
                    ])
                ->color('primary'),

            Stat::make('Active Skills', $active)
                ->description("{$inactive} inactive")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Average Proficiency', $avgProficiency ? number_format($avgProficiency, 1) . ' / 5' : 'N/A')
                ->description('Technical skills average')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning'),
        ];
    }
}
