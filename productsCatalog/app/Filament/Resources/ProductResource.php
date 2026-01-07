<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'Product';

    protected static ?string $pluralModelLabel = 'Products';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Product Information')
                ->description('Enter the product details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->placeholder('Enter product title'),

                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->maxValue(999999.99)
                        ->placeholder('0.00'),

                    Forms\Components\TextInput::make('category')
                        ->maxLength(255)
                        ->placeholder('Enter category'),

                    Forms\Components\Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Enter product description'),
                ])
                ->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Product Images')
                ->description('Add product images')
                ->schema([
                    Forms\Components\Repeater::make('images')
                        ->relationship('images')
                        ->schema([
                            Forms\Components\TextInput::make('url')
                                ->label('Image URL')
                                ->required()
                                ->url()
                                ->placeholder('https://example.com/image.jpg')
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->addActionLabel('Add Image')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['url'] ?? null),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('first_image_url')
                    ->label('Image')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function (Product $record): string {
                        // Get the first image URL
                        $imageUrl = $record->images->first()?->url;
                        
                        // Check if image URL is invalid (base64, data URI, or empty)
                        if (!$imageUrl || 
                            str_starts_with($imageUrl, 'data:image') || 
                            !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            // Return default placeholder SVG
                            return 'data:image/svg+xml,' . rawurlencode('
                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                                    <rect width="100" height="100" fill="#e5e7eb"/>
                                    <g transform="translate(50,50)">
                                        <path d="M-15,-10 L-15,10 L15,10 L15,-10 Z M-10,-15 L10,-15 L10,-5 L-10,-5 Z M-5,15 L5,15 L5,20 L-5,20 Z" fill="#9ca3af"/>
                                        <circle cx="0" cy="-5" r="3" fill="#6b7280"/>
                                    </g>
                                </svg>
                            ');
                        }
                        
                        return $imageUrl;
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->default('Uncategorized'),

                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 2))
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('images_count')
                    ->counts('images')
                    ->label('Images')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options(function () {
                        return Product::query()
                            ->distinct()
                            ->pluck('category', 'category')
                            ->filter()
                            ->toArray();
                    })
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from')
                            ->placeholder('Select date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until')
                            ->placeholder('Select date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn($q) => $q->whereDate('created_at', '>=', $data['created_from'])
                            )
                            ->when(
                                $data['created_until'],
                                fn($q) => $q->whereDate('created_at', '<=', $data['created_until'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created from ' . $data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created until ' . $data['created_until'];
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('has_images')
                    ->label('Has Images')
                    ->queries(
                        true: fn($query) => $query->has('images'),
                        false: fn($query) => $query->doesntHave('images'),
                    )
                    ->placeholder('All products')
                    ->trueLabel('With images')
                    ->falseLabel('Without images'),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading();
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 50 ? 'success' : 'warning';
    }
}