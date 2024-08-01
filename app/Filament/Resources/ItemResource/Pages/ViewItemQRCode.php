<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewItemQRCode extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected static string $view = 'filament.resource.item-resource.pages.view-qr-code';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
