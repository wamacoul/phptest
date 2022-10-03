<?php

class ManageTable
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createTables() {
        $sqlList = [
                    "CREATE TABLE IF NOT EXISTS authors (
                        id serial PRIMARY KEY,
                        nameAuthor varchar(255) NOT NULL UNIQUE

                    );",
                    "CREATE TABLE IF NOT EXISTS books (
                        id serial PRIMARY KEY,
                        author_id integer NOT NULL references authors(id),
                        nameBook varchar(255)  UNIQUE 
                     );"
            ];

        // execute each sql statement to create new tables
        foreach ($sqlList as $sql) {
            $this->pdo->exec($sql);
           // echo $sql."<br />";
        }
        
        return $this;
    }
    public function dropTables() {
        $sql = "DROP TABLE IF EXISTS books, authors, locationfolders";
        echo $sql."<br />";
        // execute each sql statement to create new tables
        $this->pdo->exec($sql);
        
        return $this;
    }

    public function insertData($data)
    {
        $this->createTables();
        // check if author is not null
        if(empty($data->author)){
            $author = $this->insertAuthor("");
        }else{
            $author = $this->insertAuthor($data->author);
        }
        // check if author is not null
        if(empty($data->name)){
            $this->insertBook("",$author);
        }else{
            $this->insertBook($data->name,$author);
        }
    }

    /**
     * insert a new row into the authors table
     * @param type $name
     * @return the id of the inserted row
     */
    public function insertAuthor($name) {
        // prepare statement for insert
        $sql = 'INSERT INTO authors(nameAuthor) VALUES(:name)';
        $stmt = $this->pdo->prepare($sql);
        // pass values to the statement
        $stmt->bindValue(':name', $name);
        try{
            // execute the insert statement
            $stmt->execute();
        }catch(\Exception $e){
            if(strpos($e, "SQLSTATE[23505]")){
                $row = $this->getAuthorByName($name);
               return $row['id'];
            }else{
                throw $e;
            }
        }        
        echo "name authors = ".$name."<br />";
        // return generated id
        return $this->pdo->lastInsertId('authors_id_seq');
    }

    public function getAuthorByName($nameAuthor){
        $this->createTables();
         // prepare statement for insert
         $sql = "SELECT * FROM authors WHERE nameauthor = '".$nameAuthor."' LIMIT 1";

         try{
            foreach($this->pdo->query($sql) as $row)
            {   
                return $row;

            }
         }catch(\Exception $e)
         {
            throw $e;
         }

    }
    public function getAuthorById($id){
        $this->createTables();
         // prepare statement for insert
         $sql = "SELECT * FROM authors WHERE id = '".$id."' LIMIT 1";

         try{
            foreach($this->pdo->query($sql) as $row)
            {
                return $row;
            }
         }catch(\Exception $e)
         {
            throw $e;
         }

    }
    public function getAuthors(){
         // prepare statement for insert
         $sql = "SELECT * FROM authors LIMIT 30";

         try{
            $authors = $this->pdo->query($sql);
            return $authors;
         }catch(\Exception $e)
         {
            throw $e;
         }

    }
    public function getBooks($authorName=null){
        $this->createTables();
         // prepare statement for insert
         if(empty($authorName))
         {
            $sql = "SELECT 
                        nameauthor,
                        namebook 
                    FROM authors 
                    LEFT JOIN books 
                    ON books.author_id = authors.id
                    LIMIT 30;
                    ";

            try{
                $books = $this->pdo->query($sql);
                
                return $books;
            }catch(\Exception $e)
            {
                throw $e;
            }
         }else{
            $sql = "SELECT 
                        nameauthor, 
                        namebook 
                    FROM 
                        books 
                    RIGHT JOIN authors 
                        ON authors.id = books.author_id
                    WHERE
                        authors.nameauthor = '".$authorName."'
                    LIMIT 30"
                    ;
            try{
                $books = $this->pdo->query($sql);              
                return $books;
            }catch(\Exception $e)
            {
                throw $e;
            }
         }

    }
    public function alterChangeBookAuthorId($name,$author_id) {
        $this->createTables();
        // prepare statement for insert
        $sql = "UPDATE books SET author_id = ".$author_id." WHERE namebook = '".$name."';";
        $stmt = $this->pdo->prepare($sql);    
        try{
            // execute the insert statement
            $stmt->execute();
        }catch(\Exception $e){
            ///die($e);
            if(strpos($e, "SQLSTATE[23505]")){
                return ;
             }else{
                 throw $e;
             }
        }        
        echo $sql."<br />";
        // return generated id
        return $this->pdo->lastInsertId('authors_id_seq');
    }

    /**
     * insert a new row into the Book table
     * @param type $name ,$author_id
     * @return the id of the inserted row
     */
    public function insertBook($name,$author_id) {
        $this->createTables();
        // prepare statement for insert
        $sql = 'INSERT INTO books(nameBook,author_id) VALUES(:names,:author_id)';
        //die($sql);
        $stmt = $this->pdo->prepare($sql);
        // pass values to the statement
        $stmt->bindValue(':names', $name);
        $stmt->bindValue(':author_id', $author_id);
        echo $sql."<br />";
        
        try{
            // execute the insert statement
            $stmt->execute();
        }catch(\Exception $e){
           // die("test");
            if(strpos($e, "SQLSTATE[23505]")){
                return $this->alterChangeBookAuthorId($name,$author_id);
             }else{
                 throw $e;
             }
        }        
        echo $sql."<br />";
        // return generated id
        return $this->pdo->lastInsertId('authors_id_seq');
    }

    /**
     * return tables in the database
     */
    public function getTables() {
        $stmt = $this->pdo->query("SELECT table_name 
                                   FROM information_schema.tables 
                                   WHERE table_schema= 'public' 
                                        AND table_type='BASE TABLE'
                                   ORDER BY table_name");
        $tableList = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tableList[] = $row['table_name'];
        }

        return $tableList;
    }

}