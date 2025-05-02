# BookShelf - Library Management System User Manual

## Introduction

Welcome to BookShelf, a web-based library management system designed to help manage book collections and borrowing. This application allows users to browse available books, borrow them, and return them. Administrators can add, edit, and delete books from the collection as well as manage users.

This user manual provides detailed information on how to use the BookShelf application, including instructions for both regular users and administrators.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Getting Started](#getting-started)
   - [Registration](#registration)
   - [Login](#login)
   - [Logout](#logout)
3. [User Features](#user-features)
   - [Browsing Books](#browsing-books)
   - [Searching Books](#searching-books)
   - [Viewing Book Details](#viewing-book-details)
   - [Borrowing Books](#borrowing-books)
   - [Returning Books](#returning-books)
   - [Managing Profile](#managing-profile)
4. [Administrator Features](#administrator-features)
   - [Adding Books](#adding-books)
   - [Editing Books](#editing-books)
   - [Deleting Books](#deleting-books)
   - [User Management](#user-management)
5. [Troubleshooting](#troubleshooting)
6. [Contact Support](#contact-support)

## System Requirements

To access the BookShelf application, you need:
- A modern web browser (Chrome, Firefox, Safari, Edge)
- Internet connection
- JavaScript enabled

## Getting Started

### Registration

To use BookShelf, you need to create an account:

1. Click the "Register" link in the top-right corner of the homepage
2. Fill in the registration form with your:
   - Username (must be unique)
   - Email address (must be unique)
   - Full name (optional)
   - Password (minimum 8 characters, must include uppercase letters, lowercase letters, and numbers)
3. Confirm your password
4. Click the "Register" button
5. Upon successful registration, you will be automatically logged in and redirected to your dashboard

### Login

To log in to your existing account:

1. Click the "Login" link in the top-right corner of the homepage
2. Enter your username or email address
3. Enter your password
4. Click the "Login" button
5. Upon successful login, you will be redirected to your dashboard

### Logout

To log out of your account:

1. Click on your username in the top-right corner of the page
2. Select "Logout" from the dropdown menu
3. You will be logged out and redirected to the homepage

## User Features

### Browsing Books

To browse all available books:

1. Click on "Books" in the navigation menu
2. The system will display all books in the library
3. Books are shown as cards with title, author, and availability status
4. Green badge means the book is available for borrowing
5. Red badge means the book is currently borrowed

### Searching Books

To search for specific books:

1. Use the search bar in the navigation menu
2. Enter keywords (book title, author, description, or ISBN)
3. Click the "Search" button or press Enter
4. The system will display books matching your search criteria

### Viewing Book Details

To view detailed information about a book:

1. Click on "View Details" on any book card
2. The system will display detailed information, including:
   - Title
   - Author
   - Description
   - Publication year
   - ISBN
   - Availability status
   - Who added the book

### Borrowing Books

To borrow an available book:

1. Log in to your account
2. Navigate to the book details page
3. If the book is available (green badge), click the "Borrow this Book" button
4. The system will mark the book as borrowed by you
5. The due date (2 weeks from borrowing) will be displayed
6. You can view all your borrowed books in "My Borrowed Books"

### Returning Books

To return a book you've borrowed:

1. Log in to your account
2. Go to "My Borrowed Books" from the dropdown menu under your username
3. Find the book you want to return
4. Click the "Return" button
5. The system will mark the book as returned and make it available for others

Alternatively, you can return a book from:
1. Your dashboard
2. The book details page

### Managing Profile

To view and update your profile:

1. Click on your username in the top-right corner
2. Select "Profile" from the dropdown menu
3. On the profile page, you can:
   - Update your email address
   - Update your full name
   - Change your password

To change your password:
1. Enter your current password
2. Enter your new password (minimum 8 characters, must include uppercase letters, lowercase letters, and numbers)
3. Confirm your new password
4. Click the "Change Password" button

## Administrator Features

Administrator accounts have additional privileges to manage the book collection and users.

### Adding Books

To add a new book (administrators only):

1. Click on "Add Book" in the navigation menu
2. Fill in the book details form:
   - Title (required)
   - Author (required)
   - Description (optional)
   - Publication year (optional)
   - ISBN (optional)
3. Click the "Add Book" button
4. The new book will be added to the collection and be available for borrowing

### Editing Books

To edit an existing book (administrators only):

1. Navigate to the book details page
2. Click the "Edit" button
3. Modify the book details as needed
4. Click the "Update Book" button
5. The book details will be updated in the system

### Deleting Books

To delete a book from the collection (administrators only):

1. Navigate to the book details page
2. Click the "Delete" button
3. Confirm the deletion in the confirmation dialog
4. The book will be permanently removed from the collection

Note: Books that are currently borrowed cannot be deleted until they are returned.

### User Management

To manage registered users (administrators only):

1. Click on "Users" in the navigation menu
2. The system will display all registered users in a table format
3. You can view details of each user including:
   - Username
   - Email
   - Full name
   - Role (administrator or regular user)
   - Registration date
4. To delete a user, click the "Delete" button next to the respective user
5. Confirm the deletion in the confirmation dialog

Note: The primary administrator cannot be deleted, and administrators cannot delete themselves.

## Troubleshooting

### Common Issues and Solutions

1. **Can't log in:**
   - Make sure you're using the correct username/email and password
   - Check if Caps Lock is turned on
   - Try resetting your password

2. **Can't borrow a book:**
   - Make sure you're logged in
   - Check if the book is available (green badge)
   - Contact the administrator if problems persist

3. **Book not showing up in searches:**
   - Try using different keywords
   - Make sure you're spelling correctly
   - Try searching by part of the title or author name

4. **Error messages:**
   - Read the error message carefully for specific instructions
   - Try refreshing the page
   - Try logging out and logging back in

## Contact Support

If you encounter any issues that can't be resolved using this manual, please contact the system administrator at:

- Email: admin@bookshelf.example.com
- Phone: +1-234-567-8900
- Support hours: Monday to Friday, 9:00 AM - 5:00 PM

## Technical Details

The BookShelf application is built using the following technologies:
- Frontend: HTML, CSS, JavaScript, Bootstrap
- Backend: PHP
- Database: MySQL

Session management is implemented to maintain user authentication across pages. The application uses secure password hashing and follows best practices for web security.