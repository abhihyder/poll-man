# Poll Management System

A Poll Management System built with Laravel 11, utilizing Breeze for authentication, Alpine.js for frontend interactivity, and Reverb WebSocket for real-time communication. This application allows admins to create polls, display them to users, and manage voting efficiently.

## Table of Contents

- [Poll Management System](#poll-management-system)
  - [Table of Contents](#table-of-contents)
  - [Prerequisites](#prerequisites)
  - [Features](#features)
  - [Technologies Used](#technologies-used)
  - [Installation](#installation)
  - [Testing](#testing)
    - [PollVoteTest](#pollvotetest)
    - [Running the Tests](#running-the-tests)
  - [Populate Admin User and Poll](#populate-admin-user-and-poll)
  - [Usage](#usage)
  - [Endpoints](#endpoints)
  - [Real-Time Updates](#real-time-updates)
    - [Client-Side Integration](#client-side-integration)
  - [Setup with Docker](#setup-with-docker)

## Prerequisites

Before you begin, ensure you have met the following requirements:

- **PHP**: Version 8.2 or higher
- **Node.js**: Version 14 or higher
- **SQLite Driver**: Ensure the SQLite driver is enabled in your PHP installation.

## Features

- **Admin Poll Creation**: A simple interface for creating polls with one question and multiple options.
- **Poll Display**: A public page for displaying poll questions and options with a shareable link.
- **Vote Submission**: An endpoint for authenticated (or guest) users to submit votes.
- **One Vote per User/IP**: Mechanism to ensure each user can only vote once.
- **Real-Time Updates**: Broadcasts updated vote counts using Laravel's event broadcasting and WebSockets.
- **Client-Side Integration**: A frontend that listens for broadcast events and updates poll results in real-time.

## Technologies Used

- **Laravel 11**: The backend framework for building the application.
- **Breeze**: For user authentication and simple UI scaffolding.
- **Alpine.js**: For enhancing frontend interactivity.
- **Reverb WebSocket**: For real-time communication and updates.
- **SQLite**: Database for storing poll and vote data.

## Installation

1. **Clone the repository**:

   ```bash
   git clone https://github.com/abhihyder/poll-man.git
   cd poll-man
   ```

2. **Install dependencies**:

   ```bash
   composer install
   npm install
   ```

3. **Build the frontend**:

   After installing the npm dependencies, run the following command to build the frontend assets:

   ```bash
   npm run build
   ```

4. **Set up your environment variables**:

   Copy the `.env.example` file to `.env` and configure your database and other settings:

   ```bash
   cp .env.example .env
   ```

   All the required values for environment variables are already defined in the `.env.example` file, making it easy for you to get started with testing.

5. **Generate the application key**:

   ```bash
   php artisan key:generate
   ```

6. **Run migrations**:

   ```bash
   php artisan migrate
   ```

7. **Start the local development server**:

   ```bash
   php artisan serve
   ```

8. **Run WebSocket server**:

   ```bash
   php artisan reverb:start
   ```

9. **Run Queue**:

   ```bash
   php artisan queue:work
   ```

## Testing

This project includes automated tests to ensure the functionality of the polling system. The following tests are implemented:

### PollVoteTest

The `PollVoteTest` includes two main test cases:

1. **User Can Vote in Poll**: 
   - This test checks that a user can successfully vote in an active poll. It verifies that the vote is recorded in the database.

2. **User Cannot Vote More Than Once**: 
   - This test ensures that a user cannot vote more than once for the same poll. If a user tries to vote again, the response will return an error indicating that they have already voted.

### Running the Tests

To run the tests, use the following command:

```bash
php artisan test --filter PollVoteTest
```

## Populate Admin User and Poll

After successfully running the tests, you can populate the database with an admin user and a sample poll using the following commands:

```bash
php artisan db:seed
```

## Usage

1. **Login Instructions**:
   Use the following credentials to log in as the admin user:
   - **Email**: `admin@example.com`
   - **Password**: `password`

2. **Public Poll Page**:
   Visit the public page (e.g., `/`) to see the available polls.

3. **Voting**:
   Users can submit their votes through the public poll page.

## Endpoints

- **GET /dashboard**: View all polls as an admin.
- **GET /**: Display all public polls.
- **POST /poll**: Create a new poll.
- **GET /poll/{uid}**: Display single poll.
- **POST /poll/vote**: Submit a vote for a poll option.

## Real-Time Updates

This application utilizes Laravel's event broadcasting capabilities to provide real-time updates. When a vote is cast, the updated vote count is broadcasted to all connected clients via Reverb WebSocket.

### Client-Side Integration

- The frontend listens for broadcast events to update the poll results in real-time using **Laravel Echo** and **Pusher.js** for WebSocket communication.
- **Alpine.js** is utilized to manage dynamic updates on the poll results page.


## Setup with Docker

If you want to set up the project with Docker, you can use the following [Docker Repository](https://github.com/abhihyder/poll-man-docker).
