<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public string $title;
    public array $authors;
    public string $type;
    public string $code;

    public function __construct(string $title, array $authors = [], string $type = '', string $code = '')
    {
        $this->title = $title;
        $this->authors = $authors;
        $this->type = $type;
        $this->code = $code;
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
            $data['code'] ?? ''
        );
    }
}
