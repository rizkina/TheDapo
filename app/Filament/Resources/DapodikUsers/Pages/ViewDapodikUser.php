<?php

namespace App\Filament\Resources\DapodikUsers\Pages;

use App\Filament\Resources\DapodikUsers\DapodikUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDapodikUser extends ViewRecord
{
    protected static string $resource = DapodikUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
