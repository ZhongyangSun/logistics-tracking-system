@echo off
setlocal

cd /d "%~dp0\.."

set DB_NAME=logistics_tracking
set ADMIN_USER=postgres
set APP_USER=logistics_user
set APP_PASSWORD=admin

set /p PGPASSWORD=Enter PostgreSQL password for %ADMIN_USER%: 

echo === Resetting and setting up database ===

echo Creating application user if it does not exist...
psql -U %ADMIN_USER% -d postgres -c "DO $$ BEGIN IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = '%APP_USER%') THEN CREATE ROLE %APP_USER% LOGIN PASSWORD '%APP_PASSWORD%'; END IF; END $$;"

echo Terminating active connections...
psql -U %ADMIN_USER% -d postgres -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='%DB_NAME%' AND pid <> pg_backend_pid();"

echo Dropping database if it exists...
dropdb -U %ADMIN_USER% --if-exists %DB_NAME%

echo Creating database...
createdb -U %ADMIN_USER% -O %APP_USER% %DB_NAME%
if errorlevel 1 (
    echo Failed to create database %DB_NAME%
    pause
    exit /b 1
)

echo Importing schema...
psql -U %ADMIN_USER% -d %DB_NAME% -f sql\schema.sql
if errorlevel 1 (
    echo Failed to import schema.sql
    pause
    exit /b 1
)

echo Importing seed data...
psql -U %ADMIN_USER% -d %DB_NAME% -f sql\seed.sql
if errorlevel 1 (
    echo Failed to import seed.sql
    pause
    exit /b 1
)

echo Granting privileges to %APP_USER%...
psql -U %ADMIN_USER% -d %DB_NAME% -c "GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO %APP_USER%;"
psql -U %ADMIN_USER% -d %DB_NAME% -c "GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO %APP_USER%;"
psql -U %ADMIN_USER% -d %DB_NAME% -c "GRANT ALL PRIVILEGES ON SCHEMA public TO %APP_USER%;"

echo === Database setup complete ===
pause