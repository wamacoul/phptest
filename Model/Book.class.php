<?php

include_once('../Database/Connection.class.php');
include_once('../Database/ManageTable.class.php');

    class Book
    {
        private $id;
        private $author;
        private $name;

        private $manageTable;

        public function __construct($pdo=null)
        {
            /* $this->id = $id;
            $this->author = $author;
            $this->name = $name; */
            $this->manageTable = new ManageTable($pdo);
        }

        public function getId()
        {
            return $this->id;
        }
        public function setId($id)
        {
            $this->id = $id;
        }
        public function getAuthor()
        {
            return $this->author;
        }
        public function setAuthor($author)
        {
            $this->author = $author;
        }
        public function getName()
        {
            return $this->name;
        }
        public function setName($name)
        {
            $this->name = $name;
        }

        public function getListBook()
        {
            $books = $this->manageTable->getBooks();
            $arrays = array();
            $i = 0;
            foreach($books as $row){
                $arrays[$i] = new Book();
                $arrays[$i]->setName($row['namebook']);
                    $author = $this->manageTable->getAuthorById($row['id']);
                $arrays[$i]->setAuthor($author['name']);
                $i++;
            }
            foreach($arrays as $row){
               print "test".$row->getId();
            }
            print_r($arrays);
            return $arrays;
        }
        public function recordBook($fileName=null)
        {
            $this->manageTable->createTables();
            $xml = simplexml_load_file($fileName);
            for($i=0; $i<sizeof($xml); $i++){
                echo "compteur ".$i." <br />";
                $this->manageTable->insertData($xml->book[$i]);
            }
        }
    }