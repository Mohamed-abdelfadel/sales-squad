# Gorgov Lead Management System Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [User Roles](#user-roles)
    - [Admin](#admin)
    - [Salesman](#salesman)
    - [Team Leader](#team-leader)
3. [Users Table](#users)
4. [Leads Table](#leads)
5. [Teams Table](#teams)
6. [Roles Table](#roles)
7. [Statuses Table](#statuses)
8. [Genders Table](#genders)
9. [Entity-Relationship Diagram (ERD)](#entity-relationship-diagram-erd)
10. [System Functionality](#system-functionality)
- [Gamification](#gamification)
- [Task Scheduling](#task-scheduling)
- [Teams and Targets](#teams-and-targets)
- [Trophies](#trophies)
11. [Technologies Used](#technologies-used)
12. [Found a Bug?](#found-a-bug)
13. [Conclusion](#conclusion)

## Introduction<a name="introduction"></a>

Gorgov Lead Management System is a robust backend solution developed using Laravel. It facilitates the sales process by managing leads, tracking sales performance, and implementing gamification elements to enhance productivity. This documentation provides an overview of the system's key components and functionality.

## User Roles<a name="user-roles"></a>

### Admin<a name="admin"></a>
- Admins have full access to the system and can manage users, teams, and system configurations.
- They can view and modify the targets, achievements, and overall system settings.

### Salesman<a name="salesman"></a>
- Salesmen are responsible for processing leads assigned to them.
- They have individual targets, and the system calculates the value added from each lead towards their target.
- Salesmen can view their progress, update lead information, and track their performance.

### Team Leader<a name="team-leader"></a>
- Team Leaders have additional responsibilities for overseeing a team of salesmen.
- They can monitor the performance of their team members and assign leads accordingly.
- Team Leaders have access to team-specific targets and achievements.

## Users Table<a name="users"></a>

| Field            | Type          | Nullable | Description                                        |
|------------------|---------------|----------|----------------------------------------------------|
| id               | int8          | NO       | Unique identifier for the user                      |
| name             | varchar(255)  | NO       | User's name                                        |
| email            | varchar(255)  | NO       | User's email address                               |
| phone_number     | varchar(255)  | NO       | User's phone number                                |
| target           | numeric(12,2) | NO       | Target sales amount for the user                   |
| current          | numeric(12,2) | NO       | Current sales amount achieved by the user          |
| call_count       | int4          | NO       | Number of calls made by the user                   |
| password         | varchar(255)  | NO       | User's password                                    |
| device_key       | text          | YES      | Device key for authentication                      |
| remember_token   | varchar(100)  | YES      | Token for persistent login                         |
| created_at       | timestamp(0)  | YES      | Timestamp of user creation                         |
| updated_at       | timestamp(0)  | YES      | Timestamp of last user update                      |
| role_id          | int8          | NO       | User's role ID (foreign key to roles table)       |
| status_id        | int8          | NO       | User's status ID (foreign key to user_statuses table) |
| team_id          | int8          | YES      | User's team ID (foreign key to teams table)       |
| gender_id        | int8          | YES      | User's gender ID (foreign key to genders table)   |

## Leads table <a name="leads"></a>

| Field           | Type          | Nullable | Description                                        |
|-----------------|---------------|----------|----------------------------------------------------|
| id              | int8          | NO       | Unique identifier for the lead                     |
| full_name       | varchar(255)  | YES      | Lead's full name                                   |
| email           | varchar(255)  | YES      | Lead's email address                               |
| phone_number    | varchar(255)  | NO       | Lead's phone number                                |
| value           | numeric(12,2) | NO       | Value added from the lead                          |
| company_name    | varchar(255)  | YES      | Lead's company name                                |
| job_title       | varchar(255)  | YES      | Lead's job title                                   |
| address         | varchar(255)  | YES      | Lead's address                                     |
| source          | varchar(255)  | YES      | Source of the lead                                 |
| comment         | text          | YES      | Additional comments on the lead                    |
| created_at      | timestamp(0)  | YES      | Timestamp of lead creation                         |
| updated_at      | timestamp(0)  | YES      | Timestamp of last lead update                      |
| status_id       | int8          | NO       | Lead's status ID (foreign key to lead_statuses table) |
| sales_id        | int8          | YES      | Salesman assigned to the lead (foreign key to users table) |
| gender_id       | int8          | YES      | Lead's gender ID (foreign key to genders table)    |

## Teams Table<a name="teams"></a>

| Field       | Type          | Nullable | Description                                  |
|-------------|---------------|----------|----------------------------------------------|
| id          | int8          | NO       | Unique identifier for the team               |
| name        | varchar(255)  | NO       | Team name                                    |
| created_at  | timestamp(0)  | YES      | Timestamp of team creation                   |
| updated_at  | timestamp(0)  | YES      | Timestamp of last team update                |

## Roles Table<a name="roles"></a>

| Field       | Type          | Nullable | Description                                  |
|-------------|---------------|----------|----------------------------------------------|
| id          | int8          | NO       | Unique identifier for the role               |
| name        | varchar(255)  | NO       | Role name                                    |
| created_at  | timestamp(0)  | YES      | Timestamp of role creation                   |
| updated_at  | timestamp(0)  | YES      | Timestamp of last role update                |

## Statuses Table<a name="statuses"></a>

| Field       | Type          | Nullable | Description                                  |
|-------------|---------------|----------|----------------------------------------------|
| id          | int8          | NO       | Unique identifier for the status             |
| name        | varchar(255)  | NO       | Status name                                  |
| created_at  | timestamp(0)  | YES      | Timestamp of status creation                |
| updated_at  | timestamp(0)  | YES      | Timestamp of last status update             |

## Genders Table<a name="genders"></a>

| Field       | Type          | Nullable | Description                                  |
|-------------|---------------|----------|----------------------------------------------|
| id          | int8          | NO       | Unique identifier for the gender             |
| name        | varchar(255)  | NO       | Gender name                                  |
| created_at  | timestamp(0)  | YES      | Timestamp of gender creation                 |
| updated_at  | timestamp(0)  | YES      | Timestamp of last gender update              |

## Entity-Relationship Diagram (ERD)<a name="entity-relationship-diagram-erd"></a>

<p align="center">
  <img src="ERD.png?raw=true" alt="Entity relation diagram"/>
</p>

## System Functionality<a name="system-functionality"></a>

### Gamification<a name="gamification"></a>

- The system incorporates gamification elements to motivate salesmen.
- Achievements and rewards are granted based on performance and meeting or exceeding targets.

### Task Scheduling<a name="task-scheduling"></a>

- Leads are scheduled and assigned to salesmen based on task scheduling.
- The system automates lead distribution to ensure an equitable workload among salesmen.

### Teams and Targets<a name="teams-and-targets"></a>

- Salesmen are organized into teams, each with specific targets.
- Team Leaders manage teams and have access to team-specific targets and achievements.

### Trophies<a name="trophies"></a>

- Trophies are awarded to individuals and teams for outstanding performance.
- The system tracks achievements and displays them as trophies to add a competitive and challenging aspect.

## Technologies Used<a name="technologies-used"></a>

The Gorgov Lead Management System is developed using the following technologies:
- Laravel

## Found a Bug?<a name="found-a-bug"></a>

If you've encountered a bug or have any issues, please report them to the system administrator. Include detailed information about the bug and steps to reproduce it, if possible.

## Conclusion<a name="conclusion"></a>

Gorgov Lead Management System is designed to streamline the lead management process, enhance sales performance, and introduce gamification for a more engaging user experience. The system's user roles, tables, and functionality contribute to a comprehensive solution for managing leads, teams, and targets effectively. For a visual representation of the system structure, If you have any further inquiries or require additional information, please contact the system administrator.
