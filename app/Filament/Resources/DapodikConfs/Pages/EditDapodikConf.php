<?php

namespace App\Filament\Resources\DapodikConfs\Pages;

use App\Filament\Resources\DapodikConfs\DapodikConfResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDapodikConf extends EditRecord
{
    protected static string $resource = DapodikConfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
