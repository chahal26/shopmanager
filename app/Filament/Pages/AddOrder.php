<?php

namespace App\Filament\Pages;

use Closure;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;

class AddOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.add-order';

    public $customer_phone, $payment_mode, $order_details, $amount_paid, $customer_name, $village_name, $reminder_date;

    protected function getFormSchema(): array 
    {
        return [
            Forms\Components\Fieldset::make('Customer Details')
                ->schema([
                    Forms\Components\TextInput::make('customer_phone')
                        ->datalist(Customer::pluck('phone')),
                ])
                ->columns(1),

            Forms\Components\Fieldset::make('Product Details')
                ->schema([
                    Forms\Components\Repeater::make('order_details')
                    ->schema([
                        Forms\Components\Select::make('product')
                            ->options([
                                'jeans' => 'Jeans',
                                'shirt' => 'Shirt',
                                'tshirt' => 'T-Shirt'
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required(),      
                    ])
                    ->reactive()
                    ->createItemButtonLabel('Add Product')
                    ->minItems(1)
                    ->columns(2),
                ])
                ->columns(1),
            
            Forms\Components\Fieldset::make('Payment Details')
                ->schema([
                    Forms\Components\TextInput::make('amount_paid')
                        ->numeric()
                        ->reactive()
                        ->helperText(function(Closure $get, $state){
                            return '&#8377;'.$this->getPending($get('order_details'), $state). ' pending';
                        })
                        ->required(),
                    Forms\Components\Select::make('payment_mode')
                        ->options([
                            'cash' => 'Cash',
                            'online' => 'Online',
                        ])
                        ->required(),
                ]),
            
            Forms\Components\Fieldset::make('More Customer Details')
                ->schema([
                    Forms\Components\TextInput::make('customer_name'),
                    Forms\Components\TextInput::make('village_name'),
                    Forms\Components\DatePicker::make('reminder_date'),
                ])
                ->hidden(fn ($get) => $this->getPending($get('order_details'), $get('amount_paid')) > 0 ? false : true)

        ];
    }

    public function getPending($order_details, $amound_paid){
        if($order_details == null){
            return 0 ;
        }
        $amound_paid = $amound_paid == '' ? 0 : $amound_paid;
        $total = 0;
        foreach($order_details as $ord){
            $price = $ord['price'] == '' ? 0 : $ord['price'] ;
            $total += $price;
        }

        return $total - $amound_paid ;
    }

    public function submit(): void
    {
        $existCheck = Customer::where('phone', $this->customer_phone);
        if($existCheck->count() == 0){
            $customer = Customer::create([
                'phone' => $this->customer_phone,
                'name' => $this->customer_name,
                'village_name' => $this->village_name,
                'reminder_date' => $this->reminder_date,
            ]);
            $customerId = $customer->id ;
        }else{
            $cust = $existCheck->first();

            if($this->customer_name != '' ){
                $cust->name = $this->customer_name ;
            }

            if($this->village_name != '' ){
                $cust->village_name = $this->village_name ;
            }

            if($this->reminder_date != '' ){
                $cust->reminder_date = $this->reminder_date ;
            }

            $cust->save();

            $customerId = $cust->id ;
        }

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'customer_id' => $customerId,
            'payment_mode' => $this->payment_mode,
        ]);

        foreach($this->order_details as $orderDetail){
            OrderDetail::create([
                'order_id' => $order->id,
                'product' => $orderDetail['product'],
                'price' => $orderDetail['price']
            ]);
        }

        Payment::create([
            'customer_id' => $customerId,
            'amount_paid' => $this->amount_paid,
            'payment_mode' => $this->payment_mode
        ]);

        
        Notification::make()
            ->title('Saved successfully')
            ->icon('heroicon-o-document-text') 
            ->iconColor('success') 
            ->send();

    }
}
