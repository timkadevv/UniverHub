<?php

$db = new SQLite3('university.db');

$db->exec("
CREATE TABLE IF NOT EXISTS users (

id INTEGER PRIMARY KEY AUTOINCREMENT,

name TEXT NOT NULL,

email TEXT UNIQUE NOT NULL,

password TEXT NOT NULL,

role TEXT DEFAULT 'student',

faculty TEXT,

group_name TEXT,

created_at DATETIME DEFAULT CURRENT_TIMESTAMP

);
");

$db->exec("
CREATE TABLE IF NOT EXISTS subjects (

id INTEGER PRIMARY KEY AUTOINCREMENT,

name TEXT NOT NULL

);
");

$db->exec("
CREATE TABLE IF NOT EXISTS materials (

id INTEGER PRIMARY KEY AUTOINCREMENT,

user_id INTEGER,

subject_id INTEGER,

title TEXT NOT NULL,

description TEXT,

file_name TEXT,

file_path TEXT,

downloads INTEGER DEFAULT 0,

created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(user_id)
REFERENCES users(id),

FOREIGN KEY(subject_id)
REFERENCES subjects(id)

);
");

$db->exec("
CREATE TABLE IF NOT EXISTS comments (

id INTEGER PRIMARY KEY AUTOINCREMENT,

user_id INTEGER,

material_id INTEGER,

text TEXT,

created_at DATETIME DEFAULT CURRENT_TIMESTAMP

);
");

$db->exec("
CREATE TABLE IF NOT EXISTS ratings (

id INTEGER PRIMARY KEY AUTOINCREMENT,

user_id INTEGER,

material_id INTEGER,

rating INTEGER CHECK(rating BETWEEN 1 AND 5)

);
");

echo "Таблицы успешно созданы!";
$db->exec("
INSERT INTO subjects(name)
VALUES
('PHP'),
('Базы данных'),
('Алгоритмы'),
('Математика');
");

$password = password_hash("admin123", PASSWORD_DEFAULT);

$db->exec("
INSERT INTO users
(name,email,password,role)
VALUES
(
'Administrator',
'admin@universityhub.com',
'$password',
'admin'
);
");