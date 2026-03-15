<?php

namespace App\Filament\Resources\GoogleDriveConfs\Pages;

use App\Filament\Resources\GoogleDriveConfs\GoogleDriveConfResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoogleDriveConfs extends ListRecords
{
    protected static string $resource = GoogleDriveConfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
