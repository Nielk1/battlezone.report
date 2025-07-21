<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleRef extends Model
{
    public string $type;
    public string $code;

    public function __construct(string $type, string $code)
    {
        $this->type = $type;
        $this->code = $code;
    }

    public static function fromArray(array $data): ArticleRef
    {
        return new self(
            $data['type'] ?? '',
            $data['code'] ?? ''
        );
    }
}
