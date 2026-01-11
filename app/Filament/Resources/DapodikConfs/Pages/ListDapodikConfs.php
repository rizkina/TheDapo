<?php

namespace App\Filament\Resources\DapodikConfs\Pages;

use App\Filament\Resources\DapodikConfs\DapodikConfResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDapodikConfs extends ListRecords
{
    protected static string $resource = DapodikConfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
