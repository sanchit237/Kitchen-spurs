# Simple CMS Application API with AI-Powered Features

This is a CMS API built using **Laravel 10** and **MySQL**, featuring **user authentication**, **role-based access**, **article management**, and AI-powered features for generating **slugs** and **summaries** for articles.

## Features

### **User Authentication**
- **Login API**: User can log in with credentials.
- **Logout API**: User can log out.
- **Seeder**: Automatically creates `Admin` and `Author` users for easy testing.

### **Content Management**
- **CRUD Operations for Articles**:
  - **Title**: Article title.
  - **Slug**: Automatically generated unique slug based on the title and content.
  - **Content**: Main body content of the article.
  - **Summary**: AI-generated summary (2-3 sentences).
  - **Category**: Multiple categories allowed for each article.
  - **Status**: Article status can be `Draft`, `Published`, or `Archived`.
  - **Published Date**: The date when the article is published.
  - **Author**: The user who created the article.

- **Slug and Summary Generation**:
  - Slug is generated asynchronously using a Language Model (LLM) based on the article’s title and content.
  - Summary is also generated asynchronously by the LLM to create a 2-3 sentence summary of the article.

### **Category Management** (Admin Only)
- Admin can **CRUD categories** for organizing articles.

### **Article Listing & Filtering**
- Filter articles based on **Category**, **Status**, and **Date Range**.

### **Role-Based Access**
- **Admin**: Can manage all articles and categories.
- **Author**: Can only manage their own articles.

## Installation

Follow these steps to set up the project:

### **Prerequisites**
Ensure the following are installed:
- **PHP** 8.1 or later.
- **Composer** (for PHP dependencies).
- **MySQL** (database).
- **WAMP** (Windows development environment for Apache, MySQL, and PHP).


### **Versions**
Below versions were used for development:
- **PHP** 8.2.0.
- **Composer** 2.6.4.
- **MySQL** 8.0.31.
- **Laravel** 10.48.25.

### **Setup**

1. **Clone the Repository**:
   - git clone https://github.com/sanchit237/Kitchen-spurs.git
   - cd Kitchen-spurs

2. **Install Dependencies: Install PHP and Laravel dependencies**:
    - composer install

3. **Set Up Environment Variables**:
    - you can copy .env.example file and create a .env file , also i will share the file via email as well.
    - update the database credentials in the .env file.
    - DB_DATABASE=kitchen_spurs
    - QUEUE_CONNECTION=database
    - OPENAI_API_KEY

4. **Run Migrations: Migrate the database to create necessary tables**:
    - php artisan migrate

5. **Seed the Database:**:
    - php artisan db:seed

5. **Run Jobs: Ensure that your queue system is running for asynchronous slug and summary generation**:
    Start the Laravel queue worker:
    - php artisan queue:work

5. **Start the Development Server: Run the Laravel development server**:
    - php artisan serve


### **API Documentation**
- Postman folder is present in the root which consists of josn files
- You can import the json files in postman to get the api documentation
    1. Environment file
    2. Collection file


### **Enum Files**

**ArticleStatusEnum**
    - This enum is used to define the possible statuses for an article. The available statuses are:

    - Draft: Value 1 – Article is in draft mode and not yet published.
    - Published: Value 2 – Article is published and visible to users.
    - Archived: Value 3 – Article is archived and no longer active.

    - Method:
    - getStatusLabel(int $status): Returns a human-readable label for the article status.

**RoleEnum**
    - This enum defines the available user roles within the system. The available roles are:

    - Admin: Value 1 – Has full access to manage all articles and categories.
    - Author: Value 2 – Can only manage their own articles.

    - Method:
    - getRoleLabel(int $role): Returns a human-readable label for the user role.

