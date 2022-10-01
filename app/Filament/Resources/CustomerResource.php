<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('village_name')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('reminder_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->default('Not Available'),
                Tables\Columns\TextColumn::make('village_name')
                    ->searchable()
                    ->default('Not Available'),
                Tables\Columns\TextColumn::make('paymentPending')
                    ->default(0),
                Tables\Columns\TextColumn::make('reminder_date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                ->action(function (Customer $record, array $data): void {
                        Payment::create([
                            'customer_id' => $record->id,
                            'amount_paid' => $data['payment']
                        ]);
                        Notification::make()
                        ->title('Payment added successfully')
                        ->success()
                        ->send();
                        
                    })
                    ->form([
                        Forms\Components\TextInput::make('payment')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(fn(?Customer $record) => $record->paymentPending)
                        ->label('Pay Pending')
                        ])
                    ->visible(fn (? Customer $record) => $record->paymentPending > 0)
                    ->color('success')
                    ->icon('heroicon-o-currency-rupee'),
                Tables\Actions\EditAction::make(),
                        
            ])
            ->bulkActions([

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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }    
}
