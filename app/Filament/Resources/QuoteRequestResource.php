<?php

namespace App\Filament\Resources;

use App\Models\QuoteRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationGroup = 'Gestión';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled(),
                        Forms\Components\TextInput::make('company')
                            ->disabled(),
                        Forms\Components\TextInput::make('whatsapp')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Datos de la Solicitud')
                    ->schema([
                        Forms\Components\TextInput::make('product_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('qty')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Imágenes y Simulación')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo Adjunto')
                            ->disabled(),
                        Forms\Components\FileUpload::make('simulation_image_path')
                            ->label('Captura del Simulador')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Gestión Interna')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'Nuevo / Sin Ver',
                                'seen' => 'Visto / En Proceso',
                                'completed' => 'Respondido / Enviado',
                                'archived' => 'Archivado',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Producto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Cant')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'new' => 'Nuevo',
                        'seen' => 'Visto',
                        'completed' => 'Respondido',
                        'archived' => 'Archivado',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'Nuevo',
                        'seen' => 'Visto',
                        'completed' => 'Respondido',
                        'archived' => 'Archivado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
