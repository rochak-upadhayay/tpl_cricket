TPL CRICKET LIVE - XAMPP SETUP

1. Copy tpl_cricket folder to:
   C:\xampp\htdocs\

2. Start Apache and MySQL in XAMPP.

3. Open:
   http://localhost/phpmyadmin/

4. Import:
   database/tpl_cricket.sql

5. Open:
   http://localhost/tpl_cricket/

ADMIN LOGIN
Username: admin
Password: admin123

ADMIN:
http://localhost/tpl_cricket/admin/login.php

USER:
http://localhost/tpl_cricket/user/matches.php

IMPORTANT:
- Change the admin password before deploying publicly.
- For production, use password_hash/password_verify instead of the demo SHA-256 login.
- Configure a proper database user instead of root with an empty password.
- Enable HTTPS on the hosting server.
- This first version uses 3-second polling for live score updates.
