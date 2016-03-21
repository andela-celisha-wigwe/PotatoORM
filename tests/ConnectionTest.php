<?php

// class ConnectionTest extends \PHPUnit_Extensions_Database_TestCase
// {
//     public function setUp()
//     {

//     }

//     public $path = __DIR__."/db/guestbook.xml";

//     public function getConnection()
//     {
//         $database = 'myguestbook';
//         $user = 'root';
//         $pass = '';
//         $pdo = new PDO ("mysql:host=localhost;dbname=orm", $user, $pass);
//         $pdo->exec('CREATE TABLE IF NOT EXISTS guestbook (id int, content text, user text, created text)');
//         return $this->createDefaultDBConnection($pdo, $database);
//     }

//     public function testCreateDataSet()
//     {
//         $tableNames = array('guestbook');
//         $dataSet = $this->getConnection()->createDataSet();
//     }

//     public function testCreateQueryTable()
//     {
//         $tableNames = array('guestbook');
//         $queryTable = $this->getConnection()->createQueryTable('guestbook', 'SELECT * FROM guestbook');
//     }

//     public function testGetRowCount()
//     {
//         $this->assertEquals(0, $this->getConnection()->getRowCount('guestbook'));
//     }

//    public function getDataSet()
//     {
//         // $comp = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet( array() );
//         // $fixPath = dirname(__FILE__)."/"."db/dbxml.xml";
//         // foreach ($fixPath as $fix) {
//         //     $path = $fx."/"."$fix.xml";
//         //     $ds = $this->createMySQLXMLDataSet($path);
//         //     $comp->addDataSet();
//         // }
//         // return $comp;
//         // $dataSet2 = $this->createFlatXMLDataSet($this->path);
//         $dataSet2 = $this->createXMLDataSet($this->path);
//         // $dataSet2 = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__.'/db/guestbook.yml');
//         return $dataSet2;
//     }

//      protected function getDataSet2()
//     {
//         $arraySet = array(
//             'guestbook' => array(
//                     array('id' => 1, 'content' => 'Hello buddy!', 'user' => 'joe', 'created' => '2010-04-24'),
//                     array('id' => 2, 'content' => 'I like it!',   'user' => null,  'created' => '2010-04-24')
//                 )
//             );

//         return new MyApp_DbUnit_ArrayDataSet($arraySet);
//     }

//     public function tnoestGetRowCount()
//     {
//         $this->assertEquals(2, $this->getConnection()->getRowCount('guestbook'));
//     }
// }
