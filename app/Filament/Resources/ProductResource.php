<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'Catálogo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Información General')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Product::class, 'slug', ignoreRecord: true),
                                Forms\Components\Textarea::make('description_short')
                                    ->rows(3)
                                    ->helperText('Texto corto para listados y cards.'),
                                Forms\Components\RichEditor::make('description_long')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Imágenes y Medios')
                            ->schema([
                                Forms\Components\FileUpload::make('image_main')
                                    ->image()
                                    ->directory('products')
                                    ->required()
                                    ->imageResizeMode('force')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('800')
                                    ->imageResizeTargetHeight('800'),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Categoría y Relaciones')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('materials')
                                    ->relationship('materials', 'name')
                                    ->multiple()
                                    ->preload(),
                                Forms\Components\Select::make('techniques')
                                    ->relationship('techniques', 'name')
                                    ->multiple()
                                    ->preload(),
                            ]),

                        Forms\Components\Section::make('Estado y Visibilidad')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_featured')
                                    ->default(false),
                                Forms\Components\Toggle::make('is_carousel')
                                    ->default(false),
                                Forms\Components\Toggle::make('allows_simulation')
                                    ->default(false),
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Section::make('SEO y Acciones')
                            ->schema([
                                Forms\Components\TextInput::make('cta_text')
                                    ->placeholder('Quiero este acabado'),
                                Forms\Components\TextInput::make('cta_url'),
                                Forms\Components\Textarea::make('whatsapp_message')
                                    ->helperText('Mensaje prellenado al enviar WhatsApp.'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_main')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Destacado'),
                Tables\Columns\ToggleColumn::make('allows_simulation')
                    ->label('Simulable'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
            ])
            ->actions([
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
