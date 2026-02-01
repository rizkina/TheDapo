<?php

namespace App\Filament\Resources\Ptks\Pages;

use App\Filament\Resources\Ptks\PtkResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPtk extends ViewRecord
{
    protected static string $resource = PtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
