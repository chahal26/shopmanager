<?php

namespace App\Filament\Resources\PartyResource\Pages;

use App\Filament\Resources\PartyResource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use App\Models\PartyPayment;
use Illuminate\Database\Eloquent\Builder;

class PaymentDetails extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = PartyResource::class;

    protected static string $view = 'filament.resources.party-resource.pages.payment-details';

    protected function getTableQuery(): Builder
    {
        return PartyPayment::query()->where('party_id',1); 
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\BadgeColumn::make('type')
                ->label('Payment Type')
                ->enum([
                    'purchase' => 'Purchase',
                    'payment' => 'Payment'
                ])
                ->colors([
                    'danger' => 'payment',
                    'success' => 'purchase',
                ]),
            Tables\Columns\TextColumn::make('amount')
                ->label('Amount'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->date(),

        ];
    }
}
