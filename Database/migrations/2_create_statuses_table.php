<?php

use Database\Schema;

$statusesTable = new Schema('statuses');
$statusesTable->id();
$statusesTable->string('name')->notNull();
$statusesTable->timestamps();

return $statusesTable;