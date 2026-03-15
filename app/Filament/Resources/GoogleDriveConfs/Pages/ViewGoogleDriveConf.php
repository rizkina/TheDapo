<?php

namespace App\Filament\Resources\GoogleDriveConfs\Pages;

use App\Filament\Resources\GoogleDriveConfs\GoogleDriveConfResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGoogleDriveConf extends ViewRecord
{
    protected static string $resource = GoogleDriveConfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
