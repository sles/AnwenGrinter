<?php

use Database\Schema;

$authorsTable = new Schema('authors');
$authorsTable->id();
$authorsTable->string('firstname')->notNull();
$authorsTable->string('lastname')->notNull();
$authorsTable->timestamps();

return $authorsTable;
