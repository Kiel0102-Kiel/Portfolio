<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    protected $fillable = [
        'uuid',
        'management_token',
        'full_name',
        'profile_image',
        'email',
        'contact_number',
        'address',
        'about_me',
        'education',
        'skills',
        'projects',
        'experience',
        'links',
        'template',
    ];

    protected function casts(): array
    {
        return [
            'education' => 'array',
            'skills' => 'array',
            'projects' => 'array',
            'experience' => 'array',
            'links' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function isRemoteProfileImage(): bool
    {
        return filled($this->profile_image) && Str::startsWith($this->profile_image, ['http://', 'https://']);
    }
}
