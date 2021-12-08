<?php

use Database\Schema;

$categoriesTable = new Schema('categories');
$categoriesTable->id();
$categoriesTable->string('name')->notNull();
$categoriesTable->timestamps();

return $categoriesTable;