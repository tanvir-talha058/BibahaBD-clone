# BibahaBD Registration System

This is a complete registration and profile management system for BibahaBD.com. The system now includes seamless integration between registration, login, and profile management.

## Features

1. **Registration with Profile Page Redirect**
   - Register using the detailed form
   - Automatic redirect to profile page after successful registration
   - All registered data is saved in database

2. **Automatic Account Creation**
   - System creates a user account during registration
   - Auto-generated login credentials are provided after registration
   - Default email: [ProfileID]@bibahabd.com
   - Default password: welcome123

3. **Profile Management**
   - View and update your profile information
   - All changes are saved to the database
   - Complete validation of all form fields

## Setup Instructions

1. **Install XAMPP**
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)

2. **Start Apache and MySQL Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services
   - Make sure both services show green status indicators

3. **Setup the Database**
   - Place all files in the `htdocs` folder (e.g., `C:\xampp\htdocs\bibahabd`)
   - Open your browser and navigate to: `http://localhost/bibahabd/setup_db_registration.php`
   - This will create the required database and tables

4. **Access the System**
   - Start with the landing page: `http://localhost/bibahabd/indext.html`
   - Click "Register" to access the registration page
   - After registration, you'll be redirected to your profile page
   - You can log out and log back in using the credentials provided

## Files Description

- **indext.html**: Main landing page with login functionality
- **register.html**: Detailed registration form with validation
- **profile.html**: Profile management page for viewing/updating user information
- **register_profile.php**: Backend API for processing registration data
- **get_profile_by_id.php**: API to fetch profile data by profile ID
- **get_register_profile.php**: API to fetch data from registration table
- **update_profile.php**: API for updating profile information
- **login.php**: API for user authentication
- **setup_db_registration.php**: Script to create the database schema
- **check_database.php**: Tool to verify database connectivity
- **test_connection.php**: Tool to test database connection

## Usage Flow

1. **Registration Process**
   - Fill in the registration form with all required fields
   - Submit the form by clicking "Update"
   - System creates your profile and user account
   - You'll see a success message with login credentials
   - You'll be automatically redirected to your profile page

2. **Profile Management**
   - After registration, you're taken to your profile page
   - You can edit your information and update your profile
   - All changes are saved to the database
   - You can log out and log back in later

3. **Login Process**
   - Go to the main page (indext.html)
   - Enter your email/profile ID and password
   - Click "Login" to access your profile

## Troubleshooting

If you encounter any issues:

1. **Verify XAMPP Services**
   - Make sure both Apache and MySQL are running in XAMPP Control Panel
   - Check for port conflicts (common ports: Apache 80/443, MySQL 3306)

2. **Database Connection Issues**
   - Run `http://localhost/bibahabd/check_database.php` to verify database status
   - If the database doesn't exist, this script will create it

3. **Profile Not Loading**
   - Check if you have a valid session by logging out and back in
   - Try clearing your browser cache and cookies
   - Make sure your registration data was saved correctly
