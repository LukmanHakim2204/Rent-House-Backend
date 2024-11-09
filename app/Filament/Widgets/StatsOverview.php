<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class StatsOverview extends BaseWidget
{
    private function getPresentage(int $from, int $to)
    {
        return $to - $from / ($to + $from / 2) * 100;
    }

    protected function getStats(): array
    {
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        $transactions = Transaction::whereStatus('approved')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        $prevTransactions = Transaction::whereStatus('approved')->whereMonth('created_at', Carbon::now()->subMonth()->month)->whereYear('created_at', Carbon::now()->subMonth()->year);
        $transactionPresentase = $this->getPresentage($prevTransactions->count(), $transactions->count());
        $revennuePresentase = $this->getPresentage($prevTransactions->sum('total_price'), $transactions->sum('total_price'));

        return [
            Stat::make('New Listing of the month', $newListing),
            Stat::make('Transaction of the month', $transactions->count())
                ->description($transactionPresentase > 0 ? "{$transactionPresentase} % increased" : "{$transactionPresentase}% descreased")
                ->descriptionIcon($transactionPresentase > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transactionPresentase > 0 ? 'success' : 'danger'),
            Stat::make('Revenue of the month', Number::currency($transactions->sum('total_price'), 'USD'))
                ->description($revennuePresentase > 0 ? "{$revennuePresentase}% increased" : "{$revennuePresentase} % decreased")
                ->descriptionIcon($revennuePresentase > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revennuePresentase > 0 ? 'success' : 'danger'),
        ];
    }
}