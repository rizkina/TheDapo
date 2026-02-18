<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Akun')
                    ->description('Data identitas Anda dikunci oleh sistem Dapodik.')
                    ->schema([
                        $this->getNameFormComponent()->disabled(), // Nama jadi read-only
                        $this->getUsernameFormComponent()->disabled(), // Username jadi read-only
                    ])->columns(2),

                Section::make('Ubah Kata Sandi')
                    ->description('Kosongkan jika tidak ingin mengubah kata sandi.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(2),
            ]);
    }

    /**
     * Karena di model kita menggunakan 'nama', bukan 'name', 
     * kita harus sesuaikan komponen name-nya.
     */
    protected function getNameFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('nama') // sesuaikan dengan kolom di DB
            ->label('Nama Lengkap')
            ->required();
    }

    protected function getUsernameFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('username')
            ->label('Username / NIP / NISN')
            ->required();
    }
}