<?php

namespace App\Filament\Resources\GoogleDriveConfs\Pages;

use App\Filament\Resources\GoogleDriveConfs\GoogleDriveConfResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGoogleDriveConf extends EditRecord
{
    protected static string $resource = GoogleDriveConfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
