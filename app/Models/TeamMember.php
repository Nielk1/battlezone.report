<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    public string $name;
    public string $position;
    public ?string $image;
    /** @var array<string, string> */
    public array $social = [];

    public function __construct(string $name, string $position = '', ?string $image = null, array $social = [])
    {
        $this->name = $name;
        $this->position = $position;
        $this->image = $image;
        $this->social = $social;
    }

    /**
     * Create a TeamMember from an array
     */
    public static function fromArray(array $data): TeamMember
    {
        return new self(
            $data['name'] ?? '',
            $data['position'] ?? '',
            $data['image'] ?? null,
            $data['social'] ?? []
        );
    }
}
