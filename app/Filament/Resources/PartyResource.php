<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartyResource\Pages;
use App\Filament\Resources\PartyResource\RelationManagers;
use App\Models\Party;
use App\Models\PartyPayment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class PartyResource extends Resource
{
    protected static ?string $model = Party::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('items_provided')
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Party Name'),
                Tables\Columns\TextColumn::make('items_provided'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->action(function (Party $record, array $data): void {
                        PartyPayment::create([
                            'party_id' => $record->id,
                            'type' => $data['type'],
                            'amount' => $data['payment']
                        ]);
                        Notification::make()
                            ->title('Payment added successfully')
                            ->success()
                            ->send();
                        
                    })
                    ->form([
                        Forms\Components\Select::make('type')
                            ->options([
                                'purchase' => 'Purchase',
                                'payment' => 'Payment',
                            ])
                            ->label('Payment Type'),
                        Forms\Components\TextInput::make('payment')
                            ->numeric()
                            ->minValue(0)
                            ->label('Amount')
                        ])
                    ->color('success')
                    ->icon('heroicon-o-currency-rupee'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('payments')
                    ->label('View Payments')
                    ->color('secondary')
                    ->url(fn (Party $record) => PartyResource::getUrl('payments',['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParties::route('/'),
            'create' => Pages\CreateParty::route('/create'),
            'edit' => Pages\EditParty::route('/{record}/edit'),
            'payments' => Pages\PaymentDetails::route('/{record}/payment-details'),
        ];
    }    
}
