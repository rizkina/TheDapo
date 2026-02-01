<?php

namespace App\Filament\Resources\Rombels\Pages;

use App\Filament\Resources\Rombels\RombelResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRombel extends ViewRecord
{
    protected static string $resource = RombelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
