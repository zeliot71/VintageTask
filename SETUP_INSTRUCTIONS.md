# VintageTask - Trello-like Dashboard Setup

## Quick Setup Guide

### Step 1: Database Migration

Before using the dashboard, you need to set up the lists table and update the tasks table.

**Option A: Using the Web Interface (Recommended)**
1. Navigate to: `http://localhost/vintagetask/run_migration.php`
2. The script will automatically:
   - Create the `lists` table
   - Add `list_id` and `card_position` columns to the `tasks` table
   - Insert 4 default lists (Trello Starter Guide, Today, This Week, Done)

**Option B: Using phpMyAdmin**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `vintagetasks_db` database
3. Go to the SQL tab
4. Copy and paste the contents of `database_lists_migration.sql`
5. Click "Go" to execute

### Step 2: Access the Dashboard

1. Start XAMPP (Apache and MySQL must be running)
2. Open your browser and go to: `http://localhost/vintagetask`
3. Login with your credentials (or sign up if you haven't already)
4. You'll be redirected to the new Trello-like dashboard

## Features

### Trello-like Board (dashboard.php)
- **Drag & Drop**: Move cards between lists using SortableJS
- **Multiple Lists**: Organize tasks in columns (To Do, Today, This Week, Done)
- **Add Cards**: Click "+ Add a card" to create new tasks
- **Add Lists**: Click "+ Add another list" to create custom columns
- **Visual Priority**: High-priority tasks are highlighted in red
- **Due Dates**: See upcoming deadlines on each card
- **Assigned Users**: Avatar initials show who's responsible

### My Tasks (tasks.php)
- **Table View**: See all your tasks in a traditional table format
- **Statistics**: Quick overview of total, in-progress, and completed tasks
- **Filtering**: View tasks by list, priority, and status

### Profile (profile.php)
- **Edit Details**: Update your name, phone, company, and position
- **Account Info**: View when you joined and your account role

## Navigation

The app includes a consistent sidebar with:
- Dashboard (Trello board)
- My Tasks (table view)
- Profile (user settings)
- Logout

## Technical Details

### Files Created/Modified:

**Core Dashboard:**
- `dashboard.php` - Main Trello-like board interface
- `tasks.php` - Task list/table view
- `profile.php` - User profile management

**Includes:**
- `includes/sidebar.php` - Left navigation sidebar
- `includes/header.php` - Top header with search
- `includes/head.php` - Updated with SortableJS CDN

**Actions (Backend):**
- `actions/update_task_position.php` - Handles drag-and-drop updates
- `actions/add_card.php` - Creates new tasks/cards
- `actions/add_list.php` - Creates new lists/columns

**Styling:**
- `assets/css/board.css` - Custom scrollbar and drag styles

**Database:**
- `database_lists_migration.sql` - Migration script
- `run_migration.php` - Web-based migration runner

### Database Schema:

**New Table: `lists`**
```sql
- id (Primary Key)
- name (List title)
- position (Order on board)
- color (Optional accent color)
- created_by (User ID)
- created_at (Timestamp)
```

**Updated Table: `tasks`**
```sql
- list_id (Foreign key to lists)
- card_position (Order within list)
```

## Troubleshooting

### "Lists table doesn't exist"
Run the migration: `http://localhost/vintagetask/run_migration.php`

### "Drag and drop not working"
Check that SortableJS is loaded. Open browser console (F12) and look for JavaScript errors.

### "Cards not appearing"
Make sure tasks have a `list_id` assigned. New cards will automatically be assigned to the list where you click "+ Add a card".

### "Empty lists showing"
This is normal if you haven't created any tasks yet. Click "+ Add a card" to create your first task.

## Browser Compatibility

The dashboard works best in modern browsers:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Next Steps

1. Run the migration
2. Create your first card
3. Try dragging cards between lists
4. Add custom lists for your workflow

Enjoy your new Trello-like task management system!
