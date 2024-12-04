-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    category INT NOT NULL,
    max_attendees INT NOT NULL,
    created_by INT,
    image_path VARCHAR(255),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create rsvps table
CREATE TABLE IF NOT EXISTS rsvps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Insert sample users
INSERT INTO users (username, email, password) VALUES
('Nafisa Latif', 'nafisa@example.com', 'nafisa'),
('KyroTheCockatiel', 'kyro@example.com', 'kyro'),
('JasperTheParakeet', 'jasper@example.com', 'jasper'),
('Bob', 'bob@example.com', 'bob'),
('Jane', 'jane@example.com', 'jane');

-- Insert sample events
INSERT INTO events (title, description, date, time, location, category, max_attendees, created_by, image_path) VALUES
('Tech Conference 2024', 'Annual tech conference', '2024-12-15', '09:00:00', 'Javits Center', 1, 500, 1, 'uploads/tech_conference.jpg'),
('Summer Music Festival', 'Outdoor music event', '2024-07-20', '14:00:00', 'Central Park', 0, 1000, 2, 'uploads/music_festival.jpg'),
('Food Truck Rally', 'Variety of food trucks', '2024-08-10', '11:00:00', 'Downtown Brooklyn', 2, 300, 3, 'uploads/food_truck_rally.jpg'),
('Art Exhibition', 'Local artists showcase', '2024-09-05', '10:00:00', 'MoMA', 3, 200, 4, 'uploads/art_exhibition.jpg'),
('Charity Run', '5K run for charity', '2024-10-01', '08:00:00', 'Riverside Park', 5, 500, 5, 'uploads/charity_run.jpg'),
('Tech Startup Pitch', 'Pitch competition for startups', '2024-11-15', '13:00:00', 'Bryant Park', 1, 150, 1, 'uploads/startup_pitch.jpg');

-- Insert sample RSVPs
INSERT INTO rsvps (user_id, event_id) VALUES
(1, 2),
(2, 1),
(3, 4),
(4, 3),
(5, 6),
(1, 5),
(2, 3),
(3, 1),
(4, 2),
(5, 4);