<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\TrendValue;

class MonthlyTransactionsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Monthly Transactions';

    protected function getData(): array
    {
        $data = Trend::model(Transaction::class)
        ->between(start:now()->startOfMonth(), end:now()->endOfMonth())->perDay()->count();
        return [
            'datasets' => [
                [
                    'label' => 'Transaction created',
                    'data' => $data->map(fn(TrendValue $value)=> $value->aggregate),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' =>  $data->map(fn(TrendValue $value)=>$value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription():string
    {
        return 'The number of transactions created per month';
    }
}