<?php

namespace MyBooks\DAO;

use MyBooks\Domain\Book;

class BookDAO extends DAO
{
	/**
	*	Return details of one book
	*
	* @return array A book with details
	*/
	public function showBookDetails($id){
		$sql = "SELECT 	book_title,
										book_summary,
										book_isbn, 
										auth_first_name, 
										auth_last_name
							FROM book 
							INNER JOIN author
							ON book.auth_id = author.auth_id
							WHERE book_id=?";
		$row = $this->getDb()->fetchAssoc($sql, array($id));
		
		return $this->buildDomainObject($row);
	}
	
	/**
	* Return a list of all books, sorted by date (most recent fisrt)
	*
	* @return array A list of all books
	*/
	public function findAll(){
		$sql = "SELECT 	*	FROM book ORDER by book_id DESC";
		$result = $this->getDb()->fetchAll($sql);
		
		// Convert query result to an array of domain objects
		$books = array();
		foreach($result as $row){
			$bookId = $row['book_id'];
			$books[$bookId] = $this->buildDomainObject($row);
		}
		return $books;
	}
	
	/**
	* Create an Book objetc based on a DB row
	*
	* @param array $row The DB containing Book data.
	* @return \MyBooks\Domain\Book
	*/
	protected function buildDomainObject(array $row){
		$book = new Book();
		
		// No book_id on showBookDetails
		if(array_key_exists('book_id', $row)){
			$book->setId($row['book_id']);
		}
		$book->setTitle($row['book_title']);
		$book->setSummary($row['book_summary']);
		
		// No ISBN neither author on findAll list
		if(array_key_exists('book_isbn', $row)){
			$book->setIsbn($row['book_isbn']);
		}
		if(array_key_exists('auth_first_name',$row) and array_key_exists('auth_last_name', $row)){
			// Concat Author First and Last Name
			$author = $row['auth_first_name'] . ' ' . $row['auth_last_name'];
			$book->setAuthor($author);
		}
		
		return $book;
	}
}