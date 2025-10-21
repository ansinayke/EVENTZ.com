# EVENTZ - Event Management Platform

A comprehensive event management system built with PHP and MySQL, featuring role-based access control for Admins, Organizers, Participants, Sponsors, and Suppliers.

## 🚀 Features

### For All Users
- **User Authentication**: Secure login and registration system
- **Role-Based Access**: Different dashboards and features for each user type
- **Social Features**: Follow other users, view profiles, and interact
- **Search Functionality**: Search for events, users, and content
- **Personalized Feed**: See events from followed organizers and preferred categories
- **Live Events**: Watch ongoing event videos (Instagram-style "Lives")

### For Organizers
- **Event Management**: Create, edit, and delete events
- **Event Status Tracking**: Automatic status updates (Upcoming → Ongoing → Completed)
- **Video Upload**: Upload short videos for ongoing events
- **Analytics Dashboard**: Track event performance, participants, and views
- **Profile Showcase**: Public profile displaying past events and statistics

### For Participants
- **Event Discovery**: Browse and search events by category, date, and popularity
- **Event Registration**: Register for events with one click
- **Portfolio**: Showcase attended events as a participation portfolio
- **Recommendations**: Get event suggestions based on interests
- **Mark Participation**: Mark attendance after event completion

### For Sponsors
- **Sponsorship Plans**: Create, edit, and delete sponsorship packages
- **Event Sponsorship**: Sponsor events with custom or predefined plans
- **Analytics**: Track sponsorships, reach, and ROI
- **Public Profile**: Display sponsorship plans and past sponsorships

### For Admins
- **Event Approval**: Review and approve/reject events before they go live
- **System Analytics**: View platform-wide statistics
- **User Management**: Monitor users across all roles
- **Content Moderation**: Delete inappropriate events

## 📋 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache or Nginx
- **Extensions**: PDO, PDO_MySQL, GD (for image processing)

## 🛠️ Installation

### Step 1: Database Setup

1. Create a new MySQL database named `eventz`:
```sql
CREATE DATABASE eventz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u your_username -p eventz < database/schema.sql
```

The schema includes:
- All necessary tables with proper relationships
- Triggers for automatic status updates
- Default categories
- Default admin user (email: `admin@eventz.com`, password: `password`)

### Step 2: Configuration

1. Edit `config/config.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'eventz');
```

2. Update the base URL and path:
```php
define('BASE_URL', 'http://localhost');
define('BASE_PATH', '/eventz_final'); // Change if your folder name is different
```

### Step 3: File Permissions

Ensure the upload directories are writable:
```bash
chmod -R 777 public/uploads/
chmod -R 777 public/uploads/avatars/
chmod -R 777 public/uploads/events/
chmod -R 777 public/uploads/videos/
```

### Step 4: Web Server Configuration

#### Apache (.htaccess is included)
The project includes an `.htaccess` file for Apache. Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Add this to your Nginx configuration:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Step 5: Access the Application

1. Navigate to: `http://localhost/eventz_final/`
2. Login with the default admin account:
   - Email: `admin@eventz.com`
   - Password: `password`

**IMPORTANT**: Change the admin password immediately after first login!

## 📁 Project Structure

```
eventz_final/
├── app/
│   ├── controllers/      # Application controllers
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── OrganizerController.php
│   │   ├── ParticipantController.php
│   │   ├── SponsorController.php
│   │   └── UserController.php
│   ├── core/            # Core framework files
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   └── Router.php
│   ├── models/          # Data models
│   │   ├── Category.php
│   │   ├── Event.php
│   │   ├── Sponsorship.php
│   │   └── User.php
│   └── views/           # View templates
│       ├── admin/
│       ├── auth/
│       ├── layouts/
│       ├── organizer/
│       ├── participant/
│       ├── shared/
│       └── sponsor/
│       ├── static/
│       └── welcome.php
├── config/
│   └── config.php       # Application configuration
├── database/
│   └── schema.sql       # Database schema
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── uploads/         # User-uploaded files
│   └── images/
├── index.php            # Application entry point
└── README.md
```

## 🔧 Key Features Explained

### Event Status Management
Events automatically transition through statuses:
- **Pending**: Awaiting admin approval
- **Approved**: Approved by admin
- **Upcoming**: Before start date
- **Ongoing**: Between start and end date
- **Completed**: After end date

### Database Schema Highlights
- **Normalized design** with proper foreign keys
- **Triggers** for automatic status updates
- **Views** for common queries (popular events, user statistics)
- **Indexes** for performance optimization
- **Many-to-many relationships** for categories, roles, and follows

### Security Features
- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- CSRF protection (implement tokens for production)
- File upload validation

## 🎨 Customization

### Adding New Categories
Categories can be added directly in the database or through the admin panel:
```sql
INSERT INTO categories (name, slug, icon, color) 
VALUES ('Your Category', 'your-category', 'fa-icon', '#hexcolor');
```

### Modifying User Roles
The system supports 5 roles by default. To add more:
1. Add role to `roles` table
2. Update controllers to handle new role
3. Create corresponding views

### Styling
All styles are in `public/css/style.css`. The design uses:
- CSS custom properties for theming
- Responsive grid layouts
- Modern UI components

## 🐛 Troubleshooting

### Issue: Events not creating
**Solution**: Check that:
1. Database connection is working
2. All required fields are filled
3. Upload directories have write permissions
4. Check error logs in browser console

### Issue: Login redirects to 404
**Solution**: 
1. Verify `BASE_PATH` in `config/config.php` matches your folder name
2. Clear browser cache and cookies
3. Check that `.htaccess` is present and `mod_rewrite` is enabled

### Issue: Images not displaying
**Solution**:
1. Check file permissions on `public/uploads/`
2. Verify image paths in database
3. Ensure uploaded files are in correct directories

### Issue: Database connection failed
**Solution**:
1. Verify MySQL is running
2. Check database credentials in `config/config.php`
3. Ensure database exists and schema is imported

## 📝 Default Credentials

**Admin Account**:
- Email: `admin@eventz.com`
- Password: `admin123`

**IMPORTANT**: Change this password immediately in production!

## 🔒 Security Recommendations for Production

1. **Change default admin password**
2. **Disable error display**: Set `display_errors = 0` in `config/config.php`
3. **Use HTTPS**: Configure SSL certificate
4. **Implement CSRF tokens**: Add to all forms
5. **Set up backups**: Regular database and file backups
6. **Update dependencies**: Keep PHP and MySQL updated
7. **Restrict file uploads**: Validate file types and sizes
8. **Use environment variables**: Store sensitive config outside web root

## 📄 License

This project is provided as-is for educational and commercial use.

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section
2. Review error logs
3. Verify configuration settings
4. Check database schema is properly imported

## 🎯 Future Enhancements

Potential features to add:
- Email notifications
- Payment integration for paid events
- Calendar integration
- Mobile app
- Advanced analytics
- Event ticketing system
- QR code check-in
- Social media integration

---

**Version**: 1.0.0  
**Last Updated**: 2025-01-20  
**Status**: Production Ready