# PotatoORM
A simple php database object relational mapping package.

##Installation

To run this package, you must have [PHP 5.5+](http://http://php.net/) and [Composer](https://getcomposer.org/) installed.

First download the package.

`$ composer require Elchroy/PotatoORM`

Install Composer.

`$ composer install`

##Usage

To use this package, ensure the following:

* A database has been set up. The database should be compatible with `PDO`.
* A table has been created in the database. E.g `CREATE TABLE 'book' (id int NOT NULL AUTOINCREMENT PRIMARY, title varchar(255), author varchar(255)), pages int`.

Once the steps above are completed:

* From the root of this directory, open the 'config.ini' file and replace with the details of you configuration above.

See Example:

```
[database]
host = localhost
username = root
password =
dbname = creations
adaptar = mysql
```

Once the database and the configurations have been setup, all is set.

####Create a custome class to extend the `PotatoModel` class.
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

**Note: Ensure that you are with the directory of the application.**

`$ phpunit`