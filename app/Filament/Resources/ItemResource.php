<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Services\PdfService;
use Dompdf\Dompdf;
use Dompdf\Options;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('qr_path')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qr_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('View QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn (Item $record): string => static::getUrl('qr-code', ['record' => $record])),
                // ->openUrlInNewTab(),
                Action::make('Download QR Code PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Item $record) {
                        $pdfService = app(PdfService::class);
                        $pdfContent = $pdfService->generatePdfWithQrCode($record->id);
                        Log::info('PDF Content Size:', ['Here size' => strlen($pdfContent)]);

                        return response()->streamDownload(function () use ($pdfContent) {
                            echo $pdfContent;
                        }, 'qr-code-item-' . $record->id . '.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    // public function generatePdfWithQrCode($recordId)
    // {
    //     // Generate QR code image in PNG format
    //     $qrCodeImage = QrCode::format('png')->size(256)->generate('Item-' . $recordId);
    //     Log::info('$qrCodeImage');
    //     // Encode QR code image to base64
    //     $encodedQrCodeImage = base64_encode($qrCodeImage);
    //     Log::info('$encodedQrCodeImage');

    //     // Log the base64 encoded QR code image for debugging
    //     Log::info('Base64 QR Code Image:', ['image' => $encodedQrCodeImage]);

    //     // Initialize Dompdf with options
    //     $options = new Options();
    //     $options->set('defaultFont', 'Arial');
    //     $options->set('isHtml5ParserEnabled', true); // Enables HTML5 support
    //     $options->set('isPhpEnabled', true); // Enables PHP support if needed

    //     $dompdf = new Dompdf($options);

    //     // Generate HTML content for the PDF
    //     $html = '<html><body>';
    //     $html .= '<h1>QR Code for Item-' . htmlspecialchars($recordId, ENT_QUOTES, 'UTF-8') . '</h1>';
    //     $html .= '<img src="data:image/png;base64,' . htmlspecialchars($encodedQrCodeImage, ENT_QUOTES, 'UTF-8') . '">';
    //     $html .= '</body></html>';

    //     // Log the HTML content for debugging
    //     Log::info('HTML Content:', ['html' => $html]);

    //     // Load HTML content into Dompdf
    //     $dompdf->loadHtml($html);

    //     // (Optional) Set paper size and orientation
    //     $dompdf->setPaper('A4', 'portrait');

    //     // Render PDF (first pass is to detect the PDF size)
    //     $dompdf->render();

    //     // Output PDF as a string
    //     return $dompdf->output();
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'view' => Pages\ViewItem::route('/{record}'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
            'qr-code' => Pages\ViewItemQRCode::route('/{record}/qr-code'),
        ];
    }
}
