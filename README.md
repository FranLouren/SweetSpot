# SweetSpot 🎾

Padel court booking system for indoor facilities in Hannover, Germany.

## Features

- **User Authentication** - Secure login/register with password hashing
- **Court Reservations** - Book padel courts with date, time, and court selection
- **Booking History** - View active reservations and past bookings
- **Admin Panel** - Manage users and all reservations
- **Session Management** - Auto-logout after 10 minutes of inactivity
- **Overlap Prevention** - Automatic detection of booking conflicts

## Tech Stack

- **Backend:** PHP 8.x
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (Fetch API)
- **Styling:** Bootstrap 5 + Custom CSS
- **Server:** Apache (XAMPP)

## Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) or similar (Apache + MySQL + PHP)

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/SweetSpot.git
   ```

2. **Move to XAMPP htdocs folder**
   ```bash
   mv SweetSpot /path/to/xampp/htdocs/
   ```

3. **Create the database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `sweetspot`
   - Import `database/estructura.sql`

4. **Configure database connection**
   - Edit `backend/config/db.php` with your credentials

5. **Add images**
   - Place logo as `frontend/img/logo.jpg`
   - Place facilities image as `frontend/img/pistas.jpg`

6. **Access the application**
   - Open http://localhost/SweetSpot/frontend/

## Project Structure

```
SweetSpot/
├── backend/
│   ├── config/
│   │   └── db.php              # Database connection
│   ├── add_reserva.php         # Create reservation
│   ├── get_reservas.php        # Fetch reservations
│   ├── delete_reserva.php      # Cancel reservation
│   ├── auth_check.php          # Session verification
│   └── admin_check.php         # Admin verification
├── frontend/
│   ├── css/
│   │   └── custom.css          # Custom styles
│   ├── js/
│   │   └── main.js             # Frontend logic
│   ├── img/                    # Images (not tracked)
│   ├── index.php               # Landing page
│   ├── login.php               # Login form
│   ├── register.php            # Registration form
│   ├── reservas.php            # Booking page
│   ├── admin.php               # Admin panel
│   └── logout.php              # Session logout
└── database/
    └── estructura.sql          # Database schema
```

## Usage

### Regular Users
1. Register or login
2. Go to "Reservas" to book a court
3. Select date, court, and starting time
4. View/cancel your bookings

### Administrators
1. Login with admin account
2. Access admin panel to:
   - View all users
   - See all reservations
   - Cancel any reservation
   - Delete users

## License

This project is part of a TFG (Final Degree Project).

## Author

Francisco Lourenço Peña
