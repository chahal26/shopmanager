<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class ViewOrders extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static string $view = 'filament.pages.view-orders';

    protected function getTableQuery(): Builder
    {
        return Order::query(); 
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('customer.phone')->label('Customer Phone'), 
            Tables\Columns\TextColumn::make('orderTotal')->label('Order Total'),
            Tables\Columns\BadgeColumn::make('payment_mode')
                ->colors([
                    'primary',
                    'success' => 'cash',
                    'warning' => 'online',
                ]), 
            Tables\Columns\TextColumn::make('created_at')
                ->date(), 
        ];
    }

}
