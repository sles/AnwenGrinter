<?php

namespace App;

use Database\DBHandler;
use DTO\BookDTO;
use Helpers\ValidationHelper;
use Models\Author;
use Models\Book;
use Models\Category;
use Models\Model;
use Models\Status;
use Repositories\AuthorRepository;
use Repositories\BookRepository;

class App
{
    /**
     * Method creates an authors, books, categories, statuses, books_authors and books_categories tables
     *
     * @return void
     */
    public function runMigrations(): void
    {
        (new DBHandler())->createTables();
    }

    public function parseData(string $dataSource): void
    {
        /** Fetching and decoding data from json to associative array */
        $books = json_decode(file_get_contents($dataSource), true);

        /** Initialization of arrays in which duplicate data will be stored */
        $authors = [];
        $categories = [];
        $statuses = [];
        $booksDuplicates = [];

        foreach ($books as $item) {
            /** Fetching formatted data */
            $transformedData = BookDTO::transform($item)->toArray();

            /** Checking book for existence in array of duplicates */
            if (in_array($transformedData['title'], $booksDuplicates, true)) {
                continue;
            }

            /**  ISBN validation */
            if (!$transformedData['isbn'] || ValidationHelper::validateIsbn($transformedData['isbn']) <= 0) {
                continue;
            }
            $this->createStatus($transformedData, $statuses);

            /** Create a book in the DB and put it to an array of duplicates */
            $book = Book::create($transformedData);
            $books[] = $book->getAttribute('title');

            /** Create author -> books relation */
            if (!empty($item['authors'])) {
                $this->createAuthor($item['authors'], $authors, $book);
            }

            /**
             * Create book -> category relation
             */
            if (!empty($item['categories'])) {
                $this->createCategory($item['categories'], $categories, $book);
            }
        }
    }

    /**
    /* execute 2 predefined queries to showcase the results and prints output in console:
     *    1. get top 3 authors by the number of books published
     *    2. get all books by author "Jon Skeet"
     *
     * @return void
     */
    public function runQueries(): void
    {
        var_dump(AuthorRepository::top3ByPublishedBooks());
        var_dump(BookRepository::booksByAuthor('Jon Skeet'));
    }

    /**
     * Check status for existence in array of duplicates.
     * If the status does not exist in array of duplicates, add the record, otherwise get it`s id
     *
     * @param  array  $transformedData
     * @param  array  $statuses
     * @return array
     */
    protected function createStatus(array &$transformedData, array &$statuses): array
    {
        /**
         * Check status for existence in array of duplicates.
         * If the status does not exist in array of duplicates, add the record, otherwise get it`s id
         */
        if (!array_key_exists($transformedData['status_id'], $statuses)) {
            $status_id = Status::create([
                'name' => $transformedData['status_id']
            ])->getAttribute('id');

            $statuses[$transformedData['status_id']] = $status_id;
            $transformedData['status_id'] = $status_id;
        } else {
            $transformedData['status_id'] = $statuses[$transformedData['status_id']];
        }
        return $transformedData;
    }

    /**
     * Check author for existence in array of duplicates.
     * If the author does not exist, add a record, otherwise create a relation from the books_authors table
     *
     * @param  array  $authors
     * @param  array  $duplicatesArray
     * @param  Book  $book
     * @return void
     */
    protected function createAuthor(array $authors, array &$duplicatesArray, Model $book): void
    {
        foreach ($authors as $authorName) {
            /** If the author has no name, skip it */
            if (!$authorName) {
                continue;
            }

            /**
             * Checking author for existence in array of duplicates.
             * If the author does not exist, add a record, otherwise create a relation in the books_authors table
             */
            if (!array_key_exists($authorName, $duplicatesArray)) {
                $nameParts = explode(' ', $authorName);

                $firstname = $nameParts[0];
                $lastname = array_splice($nameParts, 1);

                /** Creating an author and adding it to array of duplicates */
                $author_id = Author::create([
                    'firstname' => $firstname,
                    'lastname' => implode(' ', $lastname)
                ])->getAttribute('id');
                $duplicatesArray[$authorName] = $author_id;
            }

            $dbh = new DBHandler();
            $dbh->insert('books_authors', [
                'book_id' => $book->getAttribute('id'),
                'author_id' => $duplicatesArray[$authorName]
            ]);
        }
    }

    /**
     * Check category for existence in array of duplicates.
     * If the category does not exist, add a record, otherwise create a relation in the books_categories table
     *
     * @param  array  $categories
     * @param  array  $duplicatesArray
     * @param  Model  $book
     * @return void
     */
    protected function createCategory(array $categories, array &$duplicatesArray, Model $book): void
    {
        foreach ($categories as $categoryName) {
            if (!array_key_exists($categoryName, $duplicatesArray)) {
                $category_id = Category::create([
                    'name' => $categoryName
                ])->getAttribute('id');

                $duplicatesArray[$categoryName] = $category_id;
            }

            $dbh = new DBHandler();
            $dbh->insert('books_categories', [
                'book_id' => $book->getAttribute('id'),
                'category_id' => $duplicatesArray[$categoryName]
            ]);
        }
    }
}