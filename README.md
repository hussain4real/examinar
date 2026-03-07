# Examinar

A LAN-based exam platform for schools. Teachers create and manage exams through an admin panel, start live sessions, and monitor students in real time. Students join from any device on the network, answer questions, and get instant results.

**Key features:**

- Exam & question management (MCQ, True/False) with CSV import
- Live exam sessions with real-time student monitoring
- Anti-cheat detection (tab switches, fullscreen exits, copy attempts)
- Automatic grading with pass/fail thresholds
- Student kick capability during live sessions
- Two-factor authentication (2FA) for admin accounts

## Prerequisites

Install the following on the Windows machine that will act as the server:

1. **[Laravel Herd](https://herd.laravel.com)** (recommended) — One-click installer that bundles PHP, Composer, and Node.js. Made by the Laravel team.

   Alternatively, install these manually:
   - [PHP 8.2+](https://www.php.net/downloads)
   - [Composer](https://getcomposer.org/download/)
   - [Node.js 18+](https://nodejs.org/) (includes npm)

2. **[Git](https://git-scm.com/download/win)** (optional — only needed if cloning the repository)

> **Herd tip:** After installing Herd, you can link the project folder to get a local `examinar.test` domain for admin access on the server machine. Students on other machines will still connect via the server's LAN IP address.

## Quick Start

### 1. Get the project

Clone the repository or extract the downloaded ZIP into a folder:

```bash
git clone https://github.com/hussain4real/examinar.git
cd examinar
```

### 2. Run the setup script

```bash
composer setup
```

This single command will:
- Install PHP dependencies
- Copy `.env.example` to `.env`
- Generate an application key
- Create the SQLite database file
- Run database migrations
- Install Node.js dependencies
- Build the frontend assets

### 3. Seed sample data

```bash
php artisan db:seed
```

This creates demo accounts and a sample quiz so you can explore immediately.

### 4. Configure for LAN access

Open the `.env` file in a text editor and update these values:

```dotenv
APP_NAME="Your School Name"
APP_URL=http://YOUR_LAN_IP:8000

REVERB_HOST="YOUR_LAN_IP"
VITE_REVERB_HOST="YOUR_LAN_IP"
```

Replace `YOUR_LAN_IP` with the server machine's LAN IP address (e.g., `192.168.1.100`). You can find it by running `ipconfig` in Command Prompt and looking for the **IPv4 Address** under your active network adapter.

> **Important:** After changing any `VITE_*` variable, you must rebuild the frontend:
>
> ```bash
> npm run build
> ```

### 5. Start the server

**On Windows:**

```bash
composer run dev:win
```

**On macOS/Linux:**

```bash
composer run dev
```

> **Why two commands?** Laravel Pail (log viewer) requires the `pcntl` PHP extension, which is not available on Windows. The `dev:win` script skips Pail — everything else works the same.

This starts all required services concurrently:
- **Web server** — `http://localhost:8000`
- **Queue worker** — processes background jobs
- **Log viewer** — streams application logs
- **Vite** — frontend asset compilation
- **Reverb** — WebSocket server for real-time features (port 8080)

Students can now connect from any device on the same network at:

```
http://YOUR_LAN_IP:8000
```

> The admin panel dashboard includes a **Server Info** widget that displays the current LAN IP address — useful for sharing with students.

## Default Accounts

After seeding, these accounts are available:

| Role    | Email                    | Password   |
|---------|--------------------------|------------|
| Admin   | `admin@examinar.com`     | `password` |
| Student | `student1@examinar.com`  | `password` |
| Student | `student2@examinar.com`  | `password` |
| Student | `student3@examinar.com`  | `password` |
| Student | `student4@examinar.com`  | `password` |
| Student | `student5@examinar.com`  | `password` |

> **Change the default passwords immediately after first login.** The admin panel is at `/admin`.

## How It Works (Exam Day)

1. **Admin** logs in at `http://YOUR_LAN_IP:8000/admin`
2. **Admin** creates an exam with questions (or imports questions via CSV)
3. **Admin** creates an exam session and sets it to **Active**
4. **Students** open `http://YOUR_LAN_IP:8000` on their devices, log in, and enter the lobby
5. **Students** see the active session and join the exam
6. **Admin** monitors student progress in real time from the session page
7. When time is up, **Admin** ends the session — all in-progress attempts are auto-graded
8. **Students** view their results immediately

## Windows Firewall

If students cannot connect, you may need to allow inbound connections through Windows Firewall:

1. Open **Windows Defender Firewall with Advanced Security**
2. Click **Inbound Rules** → **New Rule...**
3. Select **Port** → **Next**
4. Select **TCP**, enter **8000, 8080** → **Next**
5. Select **Allow the connection** → **Next**
6. Check **Private** (and **Domain** if on a school domain) → **Next**
7. Name it `Examinar` → **Finish**

- **Port 8000** — Web server (HTTP)
- **Port 8080** — Reverb WebSocket server (real-time features)

## Resetting for a New Exam Day

To start fresh with a clean database (removes all data):

```bash
php artisan migrate:fresh --seed
```

This recreates all tables and re-seeds the default accounts and sample quiz.

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Students can't access the site | Check Windows Firewall (see above). Verify the LAN IP with `ipconfig`. Ensure the dev server is running. |
| `pcntl` extension error on Windows | Use `composer run dev:win` instead of `composer run dev`. Pail is not supported on Windows. |
| Real-time features not working (students don't appear in lobby) | Verify `VITE_REVERB_HOST` in `.env` matches the server's LAN IP. Rebuild with `npm run build`. Check that Reverb is running (look for "reverb" in the terminal output). |
| "Unable to locate file in Vite manifest" error | Run `npm run build` to compile frontend assets. |
| Database errors after update | Run `php artisan migrate` to apply new migrations. |
| Need to change the LAN IP | Update `APP_URL`, `REVERB_HOST`, and `VITE_REVERB_HOST` in `.env`, then run `npm run build`. |
| Admin panel not accessible | Navigate to `/admin`. Only users with the `admin` role can access it. |

## Available Commands

| Command | Description |
|---------|-------------|
| `composer setup` | Full first-time setup (install deps, configure, migrate, build) |
| `composer run dev` | Start all services (macOS/Linux) |
| `composer run dev:win` | Start all services (Windows — skips Pail log viewer) |
| `php artisan db:seed` | Seed demo accounts and sample quiz |
| `php artisan migrate:fresh --seed` | Reset database and re-seed |
| `php artisan serve --host=0.0.0.0` | Start web server bound to all network interfaces |
| `php artisan reverb:start` | Start the WebSocket server |
| `npm run build` | Rebuild frontend assets (required after `.env` changes to `VITE_*` vars) |

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS 4
- **Admin Panel:** Filament 5
- **Real-time:** Laravel Reverb (WebSocket)
- **Database:** SQLite (default), MySQL/PostgreSQL supported
- **Auth:** Laravel Fortify with 2FA support

## License

All rights reserved.
