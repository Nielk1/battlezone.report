<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public string $name;
    public ?string $type;
    public ?string $icon;
    public ?string $code;
    public ?string $url;
    /** @var ChannelButton[] */
    public array $buttons;
    /** @var Channel[] */
    public array $children;

    public function __construct(
        string $name,
        ?string $type = null,
        ?string $icon = null,
        ?string $code = null,
        ?string $url = null,
        array $buttons = [],
        array $children = []
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->icon = $icon;
        $this->code = $code;
        $this->url = $url;
        $this->buttons = $buttons;
        $this->children = $children;
    }

    // Returns [any children have icon, all child has icon]
    public function childrenHaveIcon(): array
    {
        $anyTrue = false;
        foreach ($this->children as $child) {
            if ($child->icon)
                $anyTrue = true;
            else
                return [ $anyTrue, false];
        }
        return [ $anyTrue, $anyTrue ];
    }

    /**
     * Create a Channel from an array (recursive)
     */
    public static function fromArray(array $data): Channel
    {
        $children = [];
        if (!empty($data['children'])) {
            foreach ($data['children'] as $child) {
                $children[] = self::fromArray($child);
            }
        }
        $buttons = [];
        foreach ($data['buttons'] ?? [] as $buttonData) {
            $buttons[] = ChannelButton::fromArray($buttonData);
        }
        return new self(
            $data['name'] ?? null,
            $data['type'] ?? null,
            $data['icon'] ?? null,
            $data['code'] ?? null,
            $data['url'] ?? null,
            $buttons,
            $children
        );
    }
}
