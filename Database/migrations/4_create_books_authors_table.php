<?php

use Database\Schema;

$booksAuthorsTable = new Schema('books_authors');
$booksAuthorsTable->int('book_id', true);
$booksAuthorsTable->int('author_id', true);
$booksAuthorsTable->primaryKey(['book_id', 'author_id']);
$booksAuthorsTable
    ->foreignKey('book_id')
    ->references('books')
    ->on('id');
$booksAuthorsTable
    ->foreignKey('author_id')
    ->references('authors')
    ->on('id');

return $booksAuthorsTable;
