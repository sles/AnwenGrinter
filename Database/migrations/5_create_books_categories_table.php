<?php

use Database\Schema;

$booksCategoriesTable = new Schema('books_categories');
$booksCategoriesTable->int('book_id', true);
$booksCategoriesTable->int('category_id', true);
$booksCategoriesTable->primaryKey(['category_id', 'book_id']);
$booksCategoriesTable
    ->foreignKey('category_id')
    ->references('categories')
    ->on('id');
$booksCategoriesTable
    ->foreignKey('book_id')
    ->references('books')
    ->on('id');

return $booksCategoriesTable;