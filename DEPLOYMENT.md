# TCExam Deployment Guide

This guide explains how to deploy TCExam to various free hosting platforms.

## Prerequisites
1.  **Local Database Export**: 
    - Export your local database as an `.sql` file (using phpMyAdmin or `mysqldump`).
    - If you haven't set up a local database yet, use the files in the `install/` directory once the app is live.

---

## Option 1: InfinityFree / 000webhost (Easiest)
These platforms provide traditional PHP/MySQL hosting with a Control Panel.

### Steps:
1.  **Sign up**: Create an account on [InfinityFree](https://infinityfree.net/) or [000webhost](https://www.000webhost.com/).
2.  **Create Database**:
    - Go to **MySQL Databases** in your control panel.
    - Create a new database and a user. **Keep the credentials handy.**
3.  **Upload Files**:
    - Use the **Online File Manager** or an FTP client (like FileZilla).
    - Upload all files from your local `Tcexam/` folder to the `htdocs/` (InfinityFree) or `public_html/` (000webhost) directory.
4.  **Configure Database**:
    - You don't need to edit files! Because we made the config dynamic, you can just go to `yourdomain.com/install/install.php` once files are uploaded.
    - Alternatively, manually edit `shared/config/tce_db_config.php` with the credentials provided by the host.
5.  **Import Data**:
    - Use **phpMyAdmin** on the host to import your `.sql` file.

---

## Option 2: Railway / Render (Modern / Git-based)
These platforms use the `Dockerfile` we created to build your app automatically.

### Railway Steps:
1.  **Connect GitHub**: Push your code to a GitHub repository and connect it to [Railway](https://railway.app/).
2.  **Add MySQL**: Click "New" -> "Database" -> "Add MySQL".
3.  **Set Environment Variables**:
    - In your Railway service settings, add these variables:
      - `DATABASE_HOST`: (Copy from Railway MySQL service)
      - `DATABASE_NAME`: `railway`
      - `DATABASE_USER`: `root`
      - `DATABASE_PASSWORD`: (Copy from Railway MySQL service)
4.  **Deploy**: Railway will see the `Dockerfile` and deploy automatically.

### Render Steps:
1.  **Create Web Service**: Connect your GitHub repo to [Render](https://render.com/).
2.  **Environment**: Choose "Docker" as the environment.
3.  **Database**: Create a "New PostgreSQL" or "New MySQL" (Render MySQL is paid, so use a free external MySQL like [Aiven](https://aiven.io/) if needed).
4.  **Environment Variables**: Add the same `DATABASE_` variables mentioned above.

---

## Important Post-Deployment Steps
1.  **Permissions**: Ensure the following folders are writable (usually 777 or 755 depending on host):
    - `cache/`
    - `admin/backup/`
    - `images/`
2.  **Security**: After installation is complete, delete or rename the `install/` directory to prevent unauthorized resets.
