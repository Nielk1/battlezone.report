<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public string $name;
    public ?string $type;
    public ?string $icon;
    public ?string $action;
    public array $buttons;
    /** @var Channel[] */
    public array $children;

    public function __construct(
        string $name,
        ?string $type = null,
        ?string $icon = null,
        ?string $action = null,
        array $buttons = [],
        array $children = []
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->icon = $icon;
        $this->action = $action;
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
        return new self(
            $data['name'] ?? $data['code'],
            $data['type'] ?? null,
            $data['icon'] ?? null,
            $data['action'] ?? null,
            $data['buttons'] ?? [],
            $children
        );
    }
}
