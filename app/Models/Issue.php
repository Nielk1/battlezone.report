<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    public string $title;
    public string $image;
    public ?string $date;

    /** @var ArticleRef[] */
    public array $articles;

    public function __construct(string $title, string $image, ?string $date, array $articles = [])
    {
        $this->title = $title;
        $this->image = $image;
        $this->date = $date;
        $this->articles = $articles;
    }

    /**
     * Create an Issue from an array
     */
    public static function fromArray(array $data): Issue
    {
        $articles = [];
        foreach ($data['articles'] ?? [] as $articleData) {
            $articles[] = ArticleRef::fromArray($articleData);
        }
        return new self(
            $data['title'] ?? '',
            $data['image'] ?? '',
            $data['date'] ?? null,
            $articles
        );
    }
}
