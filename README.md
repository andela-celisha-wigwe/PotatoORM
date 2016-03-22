[![Coverage Status](https://coveralls.io/repos/github/andela-celisha-wigwe/PotatoORM/badge.svg?branch=develop)](https://coveralls.io/github/andela-celisha-wigwe/PotatoORM?branch=develop)
[![StyleCI](https://styleci.io/repos/53140489/shield)](https://styleci.io/repos/53140489)
[![Code Climate](https://codeclimate.com/github/andela-celisha-wigwe/PotatoORM/badges/gpa.svg)](https://codeclimate.com/github/andela-celisha-wigwe/PotatoORM)
[![Test Coverage](https://codeclimate.com/github/andela-celisha-wigwe/PotatoORM/badges/coverage.svg)](https://codeclimate.com/github/andela-celisha-wigwe/PotatoORM/coverage)
[![Issue Count](https://codeclimate.com/github/andela-celisha-wigwe/PotatoORM/badges/issue_count.svg)](https://codeclimate.com/github/andela-celisha-wigwe/PotatoORM)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andela-celisha-wigwe/PotatoORM/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/andela-celisha-wigwe/PotatoORM/?branch=develop)

# PotatoORM
A simple php database object relational mapping package.

##Installation

To run this package, you must have [PHP 5.5+](http://http://php.net/) and [Composer](https://getcomposer.org/) installed.

First download the package.

`$ composer require Elchroy/PotatoORM`

Install Composer.

`$ composer install`

##Set Up Configurations

This package supports the following database engines

* MySQL
* SQLite3

To start using this package ensure that you have already setup a database (either of the above).

* A database has been set up. The database should be compatible with `PDO`.

From the root of the application, open (or create) a file named `config.ini`.

### For MySQL

If the preferred database is `MySQL`, edit the `config.ini` file like below.

```
[database]
host = localhost
username = root
password =
dbname = name_of_database
adaptar = mysql
```

### For SQLite3

If the preferred database is `SQLite3`, edit the `config.ini` file like below.

```
[database]
adapter = sqlite
sqlite_file = name_of_database_file
```
***For SQLite3 database, ensure to store the database file in root of the appllication, same directory as the `config.ini` file.***

Once you have setup a working database, ensure to have a table in the database. Create a table unless already created.

***Note that the table name must be the same with the desired class. Also the table name must be in lowercase.***
***The table must have a column named `id` with `int` value type and must be set as the primary key of the table.***
***Otherwise, some errors might be encountered.***

The following simple SQL query will create a `book` table with 4 columns.

`CREATE TABLE 'book' (id int NOT NULL AUTOINCREMENT PRIMARY, title varchar(255), author varchar(255)), pages int`.

Once the steps above are completed:

At this point, the package is ready to communicate with the database and the table.

### Usage

####Create a custom class to extend the `PotatoModel` class.
```
class Book extends PotatoModel
{

}
```

**The class name has to be the same with the table in the database that you have setup.**

####Make a new instance of the class.
```
$book1 = new Book();

$book2 = new Book();
```

####Define some properties of the class that should be save in the database table.

**Ensure that the properties defined have the same name as the columns of the database table.**

```
// Add a first book
$book1->title = "Parry Holter : Cage of Umpires";
$book1->author = "Elchroy Cresly";
$bolt1->pages = 350;

// Add a second book
$book2->title = "Under Mountains";
$book2->author = "Zendel Shezery";
$bolt2->pages = 350;
```

####Save the new record in the table.
```
// Save the two new books.
$book1->save();
$book2->save();
```

####Find the record from the database.
```
// Find the book with ID of 2.
$book = Book::find(2);
echo $book->title;
==> "Under Mountains"
echo $book->author;
==> "Zendel Shezery"
echo $book->pages;
```

####Update a record in the database.
```
$book = Book::find(2); // '2' is the ID of the book to be found.
$book->author = "Sia Merica" // Edit the author of the book.
$book->save(); // Save the book with the new author.

// Chech if the book has been updated.
$b = Book::find(2); // The same book with the same ID.
echo $b->title;
==> "Under Mountains"
echo $b->author;
==> "Sia Merica"
```

##Test

To test this package, you can use [PHPUnit](https://phpunit.de/), from command line (WindowsOS) or terminal(MacOS).

**Note: Ensure to `cd` to root directory of the application.**

`$ phpunit`