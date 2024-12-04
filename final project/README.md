# Event Management Platform
This is an event management platform built with PHP and MySQL by Nafisa Latif.

## Setup Instructions

1. Clone this repository to your local machine.
2. Ensure you have PHP and MySQL installed on your system.
3. Create a new MySQL database for this project.
4. Copy the `config/db_connect_example.php` file to `config/db_connect.php` and update it with your own database credentials:

   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "your_database_name";

5. Import the seeder.sql file into your database to create the necessary tables and populate them with sample data:

mysql -u your_username -p your_database_name < seeder.sql

6. Configure your web server (e.g., Apache) to serve the project from the appropriate directory.


## Running the Project

1. Start your web server and MySQL database.
2. Open a web browser and navigate to the project's URL (http://localhost:8888/index.php).

## Using the Seeder File

The seeder.sql file contains SQL statements to create the necessary tables and populate them with sample data. To use it:
1. Open your MySQL client or phpMyAdmin.
2. Select your project's database.
3. Import the seeder.sql file or copy and paste its contents to execute the SQL statements.

This will set up the basic structure of your database and add some sample users and events to get you started.


## Additional Configuration
-Ensure that the uploads/ directory has write permissions for storing event images.
-Update the Google Maps API key in event_details.php if you want to use the map functionality.
-Update test_email.php and send_email.php with your email and password to test and send emails when registering and RSVPing to an event.