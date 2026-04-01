# Logistics Tracking System

A lightweight logistics tracking system built with plain PHP, PostgreSQL, and JavaScript.

This project simulates a real internal logistics management system where staff can create shipments, update delivery status, and track shipment history.

---

## Tech Stack

- Backend: PHP (plain)
- Database: PostgreSQL
- Frontend: HTML, CSS, JavaScript
- Architecture: Lightweight structured PHP (config + src + views + public)

---

## Features

### Authentication
- User login with username and password
- Session-based authentication
- Role support (admin / staff)

### Shipment Management
- Create new shipment
- View shipment list
- Search by tracking number
- View shipment details

### Status Tracking
- Update shipment status
- Maintain full status history
- Record who updated each status
- Timestamp for each update

### Dashboard
- Total shipments
- Pending / In Transit / Delivered statistics
- Recent shipments overview

### Safety & Data Integrity
- Transactions for status updates
- ON DELETE CASCADE for related records
- Input validation and sanitization

---

## Database Design

### users
- id
- username
- password_hash
- role

### shipments
- tracking_number
- sender / receiver
- origin / destination
- current_status
- created_by
- timestamps

### shipment_status_history
- shipment_id
- status
- note
- updated_by
- timestamp

---

## How to run
### 1. Setup Database
`scripts\setup_db.bat`

### 2. Start PHP server

`php -S localhost:8000 -t public`



## Demo Account

`username: sys_user`

`password: pw4admin`