<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelButton extends Model
{
    public string $icon;
    public string $name;
    public array $attr;

    public function __construct(string $icon, string $name, array $attr)
    {
        $this->icon = $icon;
        $this->name = $name;
        $this->attr = $attr;
    }

    /**
     * Create a ChannelButton from an array
     */
    public static function fromArray(array $data): ChannelButton
    {
        return new self(
            $data['icon'] ?? '',
            $data['name'] ?? '',
            $data['attr'] ?? []
        );
    }
}
