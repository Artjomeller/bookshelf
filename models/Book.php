<?php
class Book {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get all books
    public function getAll($limit = null, $offset = 0) {
        $query = "SELECT b.*, u.username as added_by_username 
                 FROM books b 
                 LEFT JOIN users u ON b.added_by = u.id 
                 ORDER BY b.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($query);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get book by ID
    public function getById($book_id) {
        $query = "SELECT b.*, u.username as added_by_username 
                 FROM books b 
                 LEFT JOIN users u ON b.added_by = u.id 
                 WHERE b.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $book_id]);
        
        return $stmt->fetch();
    }
    
    // Add a new book
    public function add($title, $author, $description, $publication_year, $isbn, $added_by) {
        $query = "INSERT INTO books (title, author, description, publication_year, isbn, added_by) 
                  VALUES (:title, :author, :description, :publication_year, :isbn, :added_by)";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':description' => $description,
                ':publication_year' => $publication_year,
                ':isbn' => $isbn,
                ':added_by' => $added_by
            ]);
            
            return [
                'success' => true,
                'book_id' => $this->pdo->lastInsertId(),
                'message' => 'Book added successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Book addition failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Update a book
    public function update($book_id, $data) {
        // Build the SET part of the query
        $set_fields = [];
        $params = [':id' => $book_id];
        
        // Only update fields that are provided
        if (isset($data['title'])) {
            $set_fields[] = "title = :title";
            $params[':title'] = $data['title'];
        }
        
        if (isset($data['author'])) {
            $set_fields[] = "author = :author";
            $params[':author'] = $data['author'];
        }
        
        if (isset($data['description'])) {
            $set_fields[] = "description = :description";
            $params[':description'] = $data['description'];
        }
        
        if (isset($data['publication_year'])) {
            $set_fields[] = "publication_year = :publication_year";
            $params[':publication_year'] = $data['publication_year'];
        }
        
        if (isset($data['isbn'])) {
            $set_fields[] = "isbn = :isbn";
            $params[':isbn'] = $data['isbn'];
        }
        
        if (isset($data['available'])) {
            $set_fields[] = "available = :available";
            $params[':available'] = $data['available'];
        }
        
        // If no fields to update
        if (empty($set_fields)) {
            return [
                'success' => false,
                'message' => 'No fields to update'
            ];
        }
        
        // Build the full query
        $query = "UPDATE books SET " . implode(', ', $set_fields) . " WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'message' => 'Book updated successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Delete a book
    public function delete($book_id) {
        $query = "DELETE FROM books WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $book_id]);
            
            return [
                'success' => true,
                'message' => 'Book deleted successfully'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Search books
    public function search($term) {
        $term = "%$term%";
        $query = "SELECT b.*, u.username as added_by_username 
                 FROM books b 
                 LEFT JOIN users u ON b.added_by = u.id 
                 WHERE b.title LIKE :term 
                 OR b.author LIKE :term 
                 OR b.description LIKE :term 
                 OR b.isbn LIKE :term 
                 ORDER BY b.created_at DESC";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':term' => $term]);
        
        return $stmt->fetchAll();
    }
    
    // Borrow a book
    public function borrow($book_id, $user_id, $due_date) {
        // Check if book is available
        $book = $this->getById($book_id);
        
        if (!$book || !$book['available']) {
            return [
                'success' => false,
                'message' => 'Book is not available for borrowing'
            ];
        }
        
        // Start a transaction
        $this->pdo->beginTransaction();
        
        try {
            // Create a loan record
            $query = "INSERT INTO book_loans (book_id, user_id, due_date, status) 
                      VALUES (:book_id, :user_id, :due_date, 'borrowed')";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':book_id' => $book_id,
                ':user_id' => $user_id,
                ':due_date' => $due_date
            ]);
            
            // Update book status to unavailable
            $query = "UPDATE books SET available = FALSE WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $book_id]);
            
            // Commit transaction
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Book borrowed successfully'
            ];
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->pdo->rollBack();
            
            return [
                'success' => false,
                'message' => 'Borrowing failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Return a book
    public function returnBook($loan_id) {
        // Get the loan information
        $query = "SELECT * FROM book_loans WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $loan_id]);
        $loan = $stmt->fetch();
        
        if (!$loan) {
            return [
                'success' => false,
                'message' => 'Loan record not found'
            ];
        }
        
        // Start a transaction
        $this->pdo->beginTransaction();
        
        try {
            // Update loan status
            $query = "UPDATE book_loans 
                      SET returned_date = CURRENT_TIMESTAMP, status = 'returned' 
                      WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $loan_id]);
            
            // Update book availability
            $query = "UPDATE books SET available = TRUE WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $loan['book_id']]);
            
            // Commit transaction
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Book returned successfully'
            ];
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->pdo->rollBack();
            
            return [
                'success' => false,
                'message' => 'Return failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Get all loans for a user
    public function getUserLoans($user_id) {
        $query = "SELECT l.*, b.title, b.author, b.isbn 
                 FROM book_loans l 
                 JOIN books b ON l.book_id = b.id 
                 WHERE l.user_id = :user_id 
                 ORDER BY l.borrowed_date DESC";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetchAll();
    }
    
    // Get active loan for a book
    public function getActiveLoan($book_id) {
        $query = "SELECT l.*, u.username, u.email 
                 FROM book_loans l 
                 JOIN users u ON l.user_id = u.id 
                 WHERE l.book_id = :book_id AND l.status = 'borrowed' 
                 LIMIT 1";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':book_id' => $book_id]);
        
        return $stmt->fetch();
    }
}