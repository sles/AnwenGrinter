## Project Overview

The main features of the project are:

1. parse a json file of a given structure
2. populate database with unique values parsed from the file provided
3. execute predefined SQL queries to test the results and print the output in console

***

### Starting the script
1. copy .env.example file and fill the correct values for connecting to your own database
2. run the following command:  
``php index.php --data-source="https://gist.githubusercontent.com/cristiberceanu/94c1539c9bd7cc0f2e3e6e12a26c1551/raw/771417ba472bf1e7c213b6684656be95898892d6/books-data-source.json" --run-queries``.

In the command above:
- --data-source - url to the JSON file of a given structure to parse
- --run-queries - executes 2 predefined queries to showcase the results and prints output in console: -
  - get top 3 authors by the number of books published
  - get all books by author "Jon Skeet"

This command triggers the following actions:
1. initialize the app
2. run the migration to create a database structure
3. get the file content provided as an argument
4. parse the file data
5. populate database with unique values
6. execute 2 predefined SQL queries to showcase the results and print their output to console