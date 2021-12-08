<?php

use Database\Schema;

$booksTable = new Schema('books');
$booksTable->id();
$booksTable->string('title')->notNull();
$booksTable->string('isbn')->notNull()->unique();
$booksTable->int('page_count', true)->default(0);
$booksTable->datetime('published_date');
$booksTable->string('thumbnail_url', 1000);
$booksTable->string('short_description', 1500);
$booksTable->string('long_description', 3000);
$booksTable->int('status_id', true);
$booksTable->timestamps();
$booksTable
    ->foreignKey('status_id')
    ->references('statuses')
    ->on('id');

return $booksTable;