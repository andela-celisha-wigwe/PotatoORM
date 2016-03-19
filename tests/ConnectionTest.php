<?php

class ConnectionTest# extends \PHPUnit_Extensions_Database_TestCase
{
    public function setUp()
    {

    }

    public function getConnection()
    {
        $database = 'myguestbook';
        $user = 'root';
        $pass = '';
        $pdo = new PDO ('mysql:host=localhost;dbname=myguestbook', $user, $pass);
        $pdo->exec('CREATE TABLE IF NOT EXISTS guestbook (id int, content text, user text, created text)');
        return $this->createDefaultDBConnection($pdo, $database);
    }

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__.'/dataSets/dbxml.xml');
    }

    public function testGetRowCount()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('guestbook'));
    }
}