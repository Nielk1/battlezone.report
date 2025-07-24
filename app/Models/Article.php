<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public string $title;
    public array $authors;
    public string $type;
    public string $code;
    public bool $hidenav = false;
    public bool $hidepermlink = false;

    public function __construct(string $title, array $authors = [], string $type = '', string $code = '', bool $hidenav = false, bool $hidepermlink = false)
    {
        $this->title = $title;
        $this->authors = $authors;
        $this->type = $type;
        $this->code = $code;
        $this->hidenav = $hidenav;
        $this->hidepermlink = $hidepermlink;
    }

    /**
     * Create an Article from an array
     */
    public static function fromArray(array $data): Article
    {
        return new self(
            $data['title'] ?? '',
            $data['authors'] ?? [],
            $data['type'] ?? '',
            $data['code'] ?? '',
            $data['hidenav'] ?? false,
            $data['hidepermlink'] ?? false
        );
    }
}
