# Design Document: Digital Library System

## Overview

The Digital Library System is a Laravel 12-based web application that provides a comprehensive platform for managing, browsing, and accessing digital books and documents. Built with modern web technologies including Livewire 3 for interactive components and Flux UI for consistent design, the system serves both administrators who manage content and users who discover and consume digital resources.

### Core Functionality

The system enables:
- **Content Management**: Administrators can upload, organize, and manage digital books with rich metadata
- **Discovery & Browsing**: Users can search, filter, and browse books through multiple pathways
- **Community Engagement**: Authenticated users can rate and review books to help others
- **Analytics**: Download tracking provides insights into content popularity
- **Secure Access**: Role-based permissions ensure appropriate access to features

### Technology Stack

- **Backend**: Laravel 12 with PHP 8.2
- **Frontend**: Livewire 3 with Volt functional components
- **UI Framework**: Flux UI Free components with Tailwind CSS 4
- **Design System**: v4.2 Brand with P3 Wide Gamut colors, mint accent, Inter typography
- **Database**: MySQL with optimized indexing
- **File Storage**: Laravel's file storage system with organized directory structure
- **Testing**: Pest 3 for comprehensive test coverage

## Architecture

### Design System Integration

The Digital Library System implements the v4.2 Brand Design System for an engaging reading experience:

#### Color Strategy & Book Categories
- **Primary Accent**: Mint (`oklch(0.7 0.28 145)`) for download buttons, ratings, and primary actions
- **Category Colors**: Each book category uses distinct P3 colors for visual organization
- **Rating System**: Mint stars for ratings with smooth hover transitions
- **Status Indicators**: Download counts and popularity metrics use mint accent variations

#### Typography for Reading Interface
- **Book Titles**: `text-2xl font-bold tracking-tighter text-balance` for optimal readability
- **Author Names**: `text-lg font-medium` in gray-700 for subtle hierarchy
- **Descriptions**: Inter regular with generous line height for comfortable reading
- **Metadata**: IBM Plex Mono for download counts, dates, and technical information

#### Visual Effects & Book Discovery
- **Book Cards**: Glassmorphism with `backdrop-blur-sm bg-white/90` for elegant depth
- **Cover Images**: Subtle shadow and hover scale effects with `duration-750 ease-in-out`
- **Search Interface**: Real-time search with smooth result transitions
- **Category Filters**: Animated filter buttons with mint accent states

#### Responsive Library Layout
- **Container Queries**: Book grids adapt using `@container` for optimal card sizing
- **Mobile Reading**: Touch-optimized interface for mobile book browsing
- **Progressive Enhancement**: Desktop features like bulk operations enhance mobile experience
- **Accessibility**: High contrast P3 colors ensure readability across all devices

## Architecture

### Application Structure

The system follows Laravel 12's streamlined architecture with clear separation of concerns:

```
app/
├── Models/                 # Eloquent models for data entities
│   ├── Book.php           # Core book entity with relationships
│   ├── Category.php       # Book categorization
│   ├── Review.php         # User reviews and ratings
│   └── User.php           # Extended user model with roles
├── Livewire/              # Livewire components for interactive UI
│   ├── BookBrowser.php    # Main book browsing interface
│   ├── BookManager.php    # Admin book management
│   ├── CategoryManager.php # Category administration
│   └── ReviewSystem.php   # Review submission and display
├── Http/
│   ├── Controllers/       # Traditional controllers for file operations
│   └── Requests/          # Form request validation classes
└── Services/              # Business logic services
    ├── BookService.php    # Book management operations
    ├── FileService.php    # File upload and storage
    └── SearchService.php  # Search and filtering logic
```

### Database Design

The system uses a normalized database schema optimized for performance:

**Books Table**:
- Primary entity storing book metadata
- Indexed on title, category_id, created_at, download_count
- Foreign key relationship to categories
- Soft deletes for data integrity

**Categories Table**:
- Simple lookup table for book organization
- Unique constraint on name field
- Cascading updates to maintain referential integrity

**Reviews Table**:
- User-generated content with ratings and comments
- Composite unique index on (user_id, book_id) to prevent duplicates
- Foreign keys to users and books with cascade deletes

**File References**:
- Stored as database columns with path references
- Organized directory structure for efficient storage
- Validation constraints for file types and sizes

### Service Layer Architecture

**BookService**: Centralizes book management logic including CRUD operations, file associations, and business rules validation.

**FileService**: Handles all file operations including upload validation, storage organization, and cleanup procedures.

**SearchService**: Optimizes search and filtering operations with caching strategies and database query optimization.

## Components and Interfaces

### Livewire Components

**BookBrowser Component**:
- Handles book discovery through search, filtering, and sorting
- Implements real-time search with debounced input
- Manages pagination and loading states
- Provides responsive grid/list view options

**BookManager Component** (Admin):
- Complete CRUD interface for book management
- File upload with drag-and-drop functionality
- Form validation with real-time feedback
- Bulk operations for efficient administration

**CategoryManager Component** (Admin):
- Simple CRUD interface for category management
- Validation to prevent deletion of categories with books
- Alphabetical sorting and search functionality

**ReviewSystem Component**:
- Star rating interface with hover effects
- Comment submission with character limits
- Review editing and deletion for own reviews
- Average rating calculation and display

### API Interfaces

**File Download Endpoint**:
```php
Route::get('/books/{book}/download', [BookController::class, 'download'])
    ->middleware('auth')
    ->name('books.download');
```

**Search API** (for AJAX requests):
```php
Route::get('/api/books/search', [BookController::class, 'search'])
    ->name('api.books.search');
```

### Component Communication

Components communicate through:
- **Livewire Events**: For real-time updates between components
- **Laravel Events**: For system-wide notifications (book downloads, reviews)
- **Session Flash Messages**: For user feedback on operations
- **Database Observers**: For automatic cleanup and maintenance tasks

## Data Models

### Book Model

```php
class Book extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'title', 'author', 'description', 'category_id',
        'cover_image_path', 'file_path', 'download_count'
    ];
    
    protected $casts = [
        'download_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationships
    public function category(): BelongsTo;
    public function reviews(): HasMany;
    
    // Computed attributes
    public function getAverageRatingAttribute(): float;
    public function getCoverUrlAttribute(): string;
    public function getFileUrlAttribute(): string;
    
    // Scopes for common queries
    public function scopePopular(Builder $query): Builder;
    public function scopeRecent(Builder $query): Builder;
    public function scopeByCategory(Builder $query, int $categoryId): Builder;
    public function scopeSearch(Builder $query, string $term): Builder;
}
```

### Category Model

```php
class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    
    // Relationships
    public function books(): HasMany;
    
    // Validation rules
    public static function rules(): array;
    
    // Scopes
    public function scopeWithBookCount(Builder $query): Builder;
    public function scopeAlphabetical(Builder $query): Builder;
}
```

### Review Model

```php
class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'book_id', 'rating', 'comment'
    ];
    
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationships
    public function user(): BelongsTo;
    public function book(): BelongsTo;
    
    // Validation
    public static function rules(): array;
    
    // Scopes
    public function scopeForBook(Builder $query, int $bookId): Builder;
    public function scopeByRating(Builder $query, int $rating): Builder;
}
```

### Database Indexes

**Performance Optimization Indexes**:
```sql
-- Books table indexes
CREATE INDEX idx_books_title ON books(title);
CREATE INDEX idx_books_category_id ON books(category_id);
CREATE INDEX idx_books_created_at ON books(created_at DESC);
CREATE INDEX idx_books_download_count ON books(download_count DESC);
CREATE INDEX idx_books_deleted_at ON books(deleted_at);

-- Reviews table indexes
CREATE UNIQUE INDEX idx_reviews_user_book ON reviews(user_id, book_id);
CREATE INDEX idx_reviews_book_id ON reviews(book_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- Categories table indexes
CREATE UNIQUE INDEX idx_categories_name ON categories(name);
```

### File Storage Structure

```
storage/app/
├── public/
│   └── books/
│       └── covers/
│           ├── 1/
│           │   └── cover.jpg
│           └── 2/
│               └── cover.png
└── private/
    └── books/
        └── files/
            ├── 1/
            │   └── book.pdf
            └── 2/
                └── document.pdf
```
## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Category Name Uniqueness

*For any* set of category names, the system should prevent creation of categories with duplicate names and ensure all existing categories have unique names.

**Validates: Requirements 1.1, 1.5**

### Property 2: Category Update Uniqueness

*For any* category update operation, the system should allow the update only if the new name doesn't conflict with existing category names.

**Validates: Requirements 1.2**

### Property 3: Category Deletion Without Books

*For any* category that has no associated books, the system should allow deletion of that category.

**Validates: Requirements 1.3**

### Property 4: Category Deletion Cascading

*For any* category that has associated books, when the category is deleted, all associated books should have their category set to null.

**Validates: Requirements 1.4**

### Property 5: Category Alphabetical Ordering

*For any* set of categories, the system should display them in alphabetical order by name.

**Validates: Requirements 1.6**

### Property 6: Book Creation with Required Fields

*For any* valid book data including title, author, description, and category assignment, the system should successfully create a book with all specified fields.

**Validates: Requirements 2.1**

### Property 7: File Upload Format Validation

*For any* file upload operation, the system should accept files in supported formats (JPEG, PNG, WebP for covers; PDF for books) and reject unsupported formats with appropriate error messages.

**Validates: Requirements 2.2, 2.3, 2.6, 7.3**

### Property 8: Book Metadata Update Preservation

*For any* book metadata update, the system should preserve existing file associations and paths.

**Validates: Requirements 2.4**

### Property 9: Book Deletion File Cleanup

*For any* book deletion, the system should remove both the database record and all associated physical files from storage.

**Validates: Requirements 2.5, 7.6**

### Property 10: Book Download Count Initialization

*For any* newly created book, the system should initialize the download count to zero.

**Validates: Requirements 2.7**

### Property 11: Automatic Timestamp Recording

*For any* book creation or update operation, the system should automatically record accurate creation and update timestamps.

**Validates: Requirements 2.8**

### Property 12: Recent Books Sorting

*For any* collection of books, when displaying recently uploaded books, the system should sort them by creation date with newest first.

**Validates: Requirements 3.1**

### Property 13: Popular Books Sorting

*For any* collection of books, when displaying popular books, the system should sort them by download count with highest first.

**Validates: Requirements 3.2**

### Property 14: Category Filtering

*For any* selected category, the system should return only books that belong to that category.

**Validates: Requirements 3.3**

### Property 15: Title Search Partial Matching

*For any* search term, the system should return books whose titles contain the search term as a substring (case-insensitive).

**Validates: Requirements 3.4**

### Property 16: Book Listing Required Information

*For any* book listing display, the system should include title, author, cover image, and category information.

**Validates: Requirements 3.5**

### Property 17: Pagination Behavior

*For any* book collection that exceeds the display limit, the system should implement pagination to manage result display.

**Validates: Requirements 3.6**

### Property 18: Book Detail Information

*For any* book detail view, the system should display description, download count, and average rating.

**Validates: Requirements 3.7**

### Property 19: Download Count Increment

*For any* book download request, the system should increment the book's download count by exactly one.

**Validates: Requirements 4.1, 4.3**

### Property 20: Authenticated Download Access

*For any* download request, the system should allow access only to authenticated users and deny access to unauthenticated users.

**Validates: Requirements 4.2, 4.6, 8.1**

### Property 21: Download Count Display

*For any* book listing, the system should display the current download count for each book.

**Validates: Requirements 4.4**

### Property 22: Missing File Error Handling

*For any* download request for a missing or corrupted file, the system should return an appropriate error message instead of failing silently.

**Validates: Requirements 4.5**

### Property 23: Review Rating Validation

*For any* review submission, the system should accept ratings from 1 to 5 stars and reject ratings outside this range.

**Validates: Requirements 5.1**

### Property 24: Optional Review Comments

*For any* review submission, the system should allow reviews with or without text comments.

**Validates: Requirements 5.2**

### Property 25: Review Uniqueness Per User

*For any* user and book combination, the system should allow only one review per user per book.

**Validates: Requirements 5.3**

### Property 26: Average Rating Calculation

*For any* book with reviews, the system should calculate and display the correct average rating based on all submitted reviews.

**Validates: Requirements 5.4**

### Property 27: Review Display Information

*For any* book's reviews, the system should display each review with user information and timestamp.

**Validates: Requirements 5.5**

### Property 28: Review Update Capability

*For any* user's existing review, the system should allow that user to update their review.

**Validates: Requirements 5.6**

### Property 29: Review Deletion Capability

*For any* user's existing review, the system should allow that user to delete their own review.

**Validates: Requirements 5.7**

### Property 30: Book Deletion Review Cleanup

*For any* book deletion, the system should automatically remove all associated reviews.

**Validates: Requirements 5.8**

### Property 31: File Storage Organization

*For any* uploaded file, the system should store it in the appropriate directory structure organized by book ID and file type.

**Validates: Requirements 7.1, 7.2**

### Property 32: Unique Filename Generation

*For any* file upload, the system should generate unique filenames to prevent conflicts even when files have identical original names.

**Validates: Requirements 7.4**

### Property 33: Database File Path References

*For any* uploaded file, the system should maintain accurate file path references in the database.

**Validates: Requirements 7.5**

### Property 34: File Upload Error Handling

*For any* invalid file upload attempt, the system should provide descriptive error messages explaining the failure reason.

**Validates: Requirements 7.7**

### Property 35: Review Authentication Requirement

*For any* review submission attempt, the system should require user authentication and reject submissions from unauthenticated users.

**Validates: Requirements 8.2**

### Property 36: Anonymous Browsing Access

*For any* browsing or search operation, the system should allow access without requiring user authentication.

**Validates: Requirements 8.3**

### Property 37: Administrative Authorization

*For any* book or category management operation, the system should restrict access to users with administrator privileges only.

**Validates: Requirements 8.4, 8.5**

## Error Handling

### File Operation Errors

**Upload Failures**: The system implements comprehensive error handling for file upload operations, including validation of file types, sizes, and storage availability. When uploads fail, users receive specific error messages indicating the failure reason (invalid format, file too large, storage unavailable).

**Download Failures**: When requested files are missing, corrupted, or inaccessible, the system returns appropriate HTTP status codes (404 for missing files, 500 for server errors) along with user-friendly error messages.

**Storage Cleanup**: Failed operations trigger automatic cleanup procedures to prevent orphaned files or incomplete database records.

### Database Operation Errors

**Constraint Violations**: The system handles database constraint violations gracefully, converting technical errors into user-friendly messages. For example, duplicate category names result in "Category name already exists" rather than raw database error messages.

**Transaction Rollback**: Complex operations involving multiple database changes use transactions to ensure data consistency. Failed operations trigger complete rollback to maintain system integrity.

**Connection Failures**: Database connection issues are handled with retry logic and fallback error pages to maintain user experience during temporary outages.

### Authentication and Authorization Errors

**Unauthorized Access**: Attempts to access protected resources without proper authentication result in redirect to login pages with appropriate return URL handling.

**Insufficient Permissions**: Users attempting administrative actions without proper roles receive clear error messages and are redirected to appropriate pages.

**Session Expiration**: Expired sessions are handled gracefully with automatic logout and session cleanup.

### Validation Errors

**Form Validation**: Client-side and server-side validation work together to provide immediate feedback for form errors while ensuring security through server-side verification.

**File Validation**: Uploaded files undergo multiple validation checks (type, size, content) with specific error messages for each failure type.

**Business Rule Violations**: Operations that violate business rules (like deleting categories with books) provide clear explanations and suggested alternatives.

## Testing Strategy

### Dual Testing Approach

The Digital Library System employs both unit testing and property-based testing to ensure comprehensive coverage and correctness:

**Unit Tests**: Focus on specific examples, edge cases, and integration points between components. These tests verify concrete scenarios and ensure proper error handling for known failure modes.

**Property Tests**: Verify universal properties across all inputs using randomized test data. These tests ensure that business rules hold true regardless of the specific data values used.

### Property-Based Testing Configuration

**Testing Framework**: The system uses Pest 3 with property-based testing capabilities to implement comprehensive property validation.

**Test Configuration**: Each property test runs a minimum of 100 iterations with randomized inputs to ensure thorough coverage of the input space.

**Property Test Tagging**: Each property-based test includes a comment tag referencing its corresponding design document property:
```php
// Feature: digital-library-system, Property 1: Category Name Uniqueness
```

### Unit Testing Focus Areas

**Specific Examples**: 
- Creating a book with valid metadata
- Uploading a valid PDF file
- Submitting a 5-star review with comment

**Edge Cases**:
- Handling empty search queries
- Managing files with special characters in names
- Processing reviews at rating boundaries (1 and 5 stars)

**Integration Points**:
- File upload with database record creation
- User authentication with download access
- Category deletion with book updates

**Error Conditions**:
- Invalid file format uploads
- Duplicate review submissions
- Unauthorized administrative actions

### Property Testing Implementation

**Category Management Properties**:
```php
test('category names must be unique', function () {
    // Generate random category names
    // Verify uniqueness constraints
    // Test duplicate prevention
})->repeat(100);
```

**File Operations Properties**:
```php
test('file uploads preserve data integrity', function () {
    // Generate random valid files
    // Upload and verify storage
    // Test file-database consistency
})->repeat(100);
```

**Search and Filtering Properties**:
```php
test('search results match query terms', function () {
    // Generate random books and search terms
    // Verify all results contain search term
    // Test case-insensitive matching
})->repeat(100);
```

### Performance Testing Considerations

While property-based tests focus on correctness, the system includes separate performance benchmarks for:
- Search response times with large datasets
- File upload handling under load
- Database query optimization verification

### Test Data Management

**Factories**: Laravel factories generate realistic test data for all models, ensuring tests use representative data structures.

**Seeders**: Database seeders create consistent test environments for integration testing.

**File Fixtures**: Test files in various formats provide consistent upload testing scenarios.

### Continuous Integration

**Automated Testing**: All tests run automatically on code changes, ensuring no regressions are introduced.

**Coverage Reporting**: Test coverage metrics ensure all critical code paths are tested.

**Property Test Monitoring**: Property test failures are analyzed to identify edge cases that may require additional unit test coverage.