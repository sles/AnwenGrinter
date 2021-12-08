<?php

namespace Repositories;

use Models\Book;

class BookRepository
{
    /**
     * Get all books by author
     *
     * @param  string  $authorName
     * @return array
     */
    public static function booksByAuthor(string $authorName): array
    {
        return Book::query()
            ->join('books_authors', 'books.id', 'books_authors.book_id')
            ->join('authors', 'books_authors.author_id', 'authors.id')
            ->where("concat_ws(' ', authors.firstname, authors.lastname)", $authorName)
            ->addSelect(['books.*'])
            ->get();
    }
}