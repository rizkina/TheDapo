<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema; 
use Filament\Forms\Components\TextInput;

class Login extends BaseLogin
{
    /**
     * Kita hapus type-hint Schema jika IDE tetap merah, 
     * atau biarkan jika Anda sudah menjalankan composer install.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLoginFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    /**
     * Kita hilangkan type-hint ': Component' di sini 
     * untuk menghentikan error Intelephense P1006 & P1009.
     */
    protected function getLoginFormComponent()
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password'  => $data['password'],
        ];
    }
}