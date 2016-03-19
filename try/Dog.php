<?php

        $dbc = new PDO("sqlite:testDB.sqlite");

        //Create a basic users table
        $db->exec('CREATE TABLE IF NOT EXISTS user (id int(25), name varchar (255), price int(10))');
        // echo "Table users has been created <br />";
        //Insert some rows
        $db->exec('INSERT INTO user (id, name, price) VALUES (1, "Bolt", 35000)');
        // echo "Inserted row into table users <br />";
        $db->exec('INSERT INTO user (id, name, price) VALUES (2, "Spyk", 25000)');
        //Insert some rows
        $db->exec('INSERT INTO user (id, name, price) VALUES (3, "Halx", 35500)');
        // echo "Inserted row into table users <br />";
        $db->exec('INSERT INTO user (id, name, price) VALUES (4, "Ferr", 28700)');
        $stmt = $dbc->prepare('SELECT * FROM user;');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);

        var_dump($result);

