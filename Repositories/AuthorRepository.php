<?php

namespace Repositories;

use Database\SelectQueryBuilder;
use Models\Author;
use Models\Book;
use Models\Model;

class AuthorRepository
{
    /**
     * Get 3 authors with the biggest amount of books published
     *
     * @return array|Model[]
     */
    public static function top3ByPublishedBooks(): array
    {
        return Author::query()
            ->addSelect(["concat_ws(' ', firstname, lastname) as full_name", 'count(books.id) as books_count'])
            ->join('books_authors', 'authors.id', 'books_authors.author_id', 'left')
            ->join('books', 'books.id', 'books_authors.book_id', 'left')
            ->where(
                'books.status_id',
                (new SelectQueryBuilder('statuses'))
                    ->where('name', Book::STATUS_PUBLISHED)
                    ->addSelect(['id'])
            )
            ->groupBy('full_name')
            ->orderBy('books_count', 'desc')
            ->limit(3)
            ->get();
    }
}