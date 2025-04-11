
# St Alphonsus Primary School Management System

## Setup Instructions

To run this application, you'll need:

1. A web server with PHP support (like Apache or Nginx)
2. MySQL or MariaDB database
3. PHP 7.0 or higher

### Installation Steps

1. **Set up your web server**
   - Make sure your web server is configured to process PHP files
   - Place all the files in your web root directory (e.g., htdocs, www, public_html)

2. **Configure the database**
   - Create a new database called `st_alphonsus`
   - Run the SQL script in `config/setup_database.sql` to create the necessary tables
   - Update `config/db_connect.php` with your database credentials

3. **Run the install script**
   - Navigate to `http://your-server/install.php` in your browser
   - This will populate the database with initial data

4. **Access the application**
   - Navigate to `http://your-server/index.php` in your browser
   - You should see the dashboard and be able to navigate to all pages

### Troubleshooting

If you see PHP code instead of rendered pages:
- Make sure your web server is configured to process PHP files
- Check that you're accessing the site through a web server (http://...) and not directly from the file system (file://...)
- Verify that PHP is installed and working properly
- Try accessing `test.php` to check if PHP is functioning correctly

If you encounter database connection issues:
- Double-check your database credentials in `config/db_connect.php`
- Make sure your database server is running
- Ensure the database and tables exist

For any other issues, please check the PHP error logs for your web server.
