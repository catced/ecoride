CREATE TABLE IF NOT EXISTS employe (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, credit INT NOT NULL, rgpd TINYINT(1) NOT NULL, is_suspended TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS vehicle (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, license_plate VARCHAR(20) NOT NULL, seats_count INT NOT NULL, energy VARCHAR(255) NOT NULL, preferences JSON DEFAULT NULL, date_first_use DATE DEFAULT NULL, UNIQUE INDEX UNIQ_1B80E486F5AA79D0 (license_plate), INDEX IDX_1B80E4867E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS ride (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT NOT NULL, driver_id INT NOT NULL, departure VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, departure_day DATE NOT NULL, price DOUBLE PRECISION NOT NULL, duration VARCHAR(255) NOT NULL, available_seats INT NOT NULL, departure_time TIME NOT NULL, status VARCHAR(20) DEFAULT 'pending' NOT NULL, INDEX IDX_9B3D7CD0545317D1 (vehicle_id), INDEX IDX_9B3D7CD0C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ;

CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ride_id INT NOT NULL, created_at DATE NOT NULL, seats_booked INT NOT NULL, INDEX IDX_E00CEDDEA76ED395 (user_id), INDEX IDX_E00CEDDE302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS review (id INT AUTO_INCREMENT NOT NULL, passenger_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, rating INT NOT NULL, validated TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_794381C64502E565 (passenger_id), INDEX IDX_794381C6302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS win_credit (id INT AUTO_INCREMENT NOT NULL, monday INT DEFAULT NULL, tuesday INT DEFAULT NULL, wednesday INT DEFAULT NULL, thursday INT DEFAULT NULL, friday INT DEFAULT NULL, saturday INT DEFAULT NULL, sunday INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ;
 SET NAMES 'utf8mb4';      
INSERT INTO employe (email,roles,password,pseudo ) VALUES ('jose@gmail.com','["ROLE_ADMIN"]','$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e','Jose');
INSERT INTO employe (email,roles,password,pseudo) VALUES ('employe@gmail.com','["ROLE_EMPLOYE"]', '$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e','Employe');

INSERT INTO user (pseudo, name, rgpd, is_suspended, credit, password, roles, email)
VALUES 
( 'roro', 'roro', 1, 0, 20, '$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e', '["ROLE_USER"]', 'roro@gmail.com'),
( 'Pierre', 'Pierre', 1, 0, 20, '$2y$13$a8J9cUOm/bby6kRaMLbZ7eJEmXpV8mWKpDRFuZgyOtr2YjI8Zi6RW', '["ROLE_USER"]', 'pierre@gmail.com'),
( 'Paul', 'Paul', 1, 0, 20, '$2y$13$a8J9cUOm/bby6kRaMLbZ7eJEmXpV8mWKpDRFuZgyOtr2YjI8Zi6RW', '["ROLE_USER"]', 'paul@gmail.com');

INSERT INTO vehicle (owner_id, brand, model, color, license_plate, seats_count, energy, preferences, date_first_use)
VALUES 
((SELECT id FROM user WHERE pseudo = 'Paul'), 'Renault', 'Laguna', 'Bleue', '148DER55', 5, 'Electrique', '["non-fumeur"]', '2021-01-01'),
((SELECT id FROM user WHERE pseudo = 'Pierre'), 'Peugeot', '308', 'Blanche', '874FGT55', 4, 'Essence', '["non-fumeur"]', '2023-06-01'),
((SELECT id FROM user WHERE pseudo = 'Paul'), 'Citroen', 'C4', 'Noire', '654SED25', 4, 'Essence', '["fumeur"]', '2023-09-03'),
((SELECT id FROM user WHERE pseudo = 'Pierre'), 'Toyota', 'Yaris', 'Rouge', '321POI77', 4, 'Hybride', '["non-fumeur"]', '2022-02-15');

INSERT INTO ride (vehicle_id, driver_id, departure, destination, departure_day, departure_time, price, duration, status, available_seats)
VALUES 
((SELECT id FROM vehicle WHERE license_plate = '148DER55'), (SELECT id FROM user WHERE pseudo = 'Paul'), 'Paris', 'Lyon', '2025-09-03', '08:30:00', 25, '04:00', 'pending', 4),
((SELECT id FROM vehicle WHERE license_plate = '654SED25'), (SELECT id FROM user WHERE pseudo = 'Paul'), 'Lyon', 'Nice', '2025-06-15', '14:30:00', 55, '06:00', 'pending', 4),
((SELECT id FROM vehicle WHERE license_plate = '874FGT55'), (SELECT id FROM user WHERE pseudo = 'Pierre'), 'Nantes', 'Paris', '2025-06-15', '12:25:00', 25, '04:15', 'pending', 4),
((SELECT id FROM vehicle WHERE license_plate = '321POI77'), (SELECT id FROM user WHERE pseudo = 'Pierre'), 'Nantes', 'Paris', '2025-09-15', '13:00:00', 25, '04:15', 'pending', 4);

INSERT INTO booking (created_at, seats_booked, user_id, ride_id)
VALUES 
('2025-06-15', 1, (SELECT id FROM user WHERE pseudo = 'Pierre'), (SELECT id FROM ride WHERE departure = 'Paris' AND destination = 'Lyon')),
('2025-06-15', 1, (SELECT id FROM user WHERE pseudo = 'roro'), (SELECT id FROM ride WHERE departure = 'Paris' AND destination = 'Lyon')),
('2025-05-10', 1, (SELECT id FROM user WHERE pseudo = 'roro'), (SELECT id FROM ride WHERE departure = 'Lyon' AND destination = 'Nice')),
('2025-04-10', 1, (SELECT id FROM user WHERE pseudo = 'Paul'), (SELECT id FROM ride WHERE departure = 'Nantes' AND destination = 'Paris' and departure_day ='20250615')),
('2025-03-01', 1, (SELECT id FROM user WHERE pseudo = 'Paul'), (SELECT id FROM ride WHERE departure = 'Nantes' AND destination = 'Paris' and departure_day ='20250915'));

INSERT INTO review (comment, rating, validated, passenger_id, ride_id)
VALUES 
('Chauffeur au top. Eau offerte', 4, 0, (SELECT id FROM user WHERE pseudo = 'roro'), (SELECT id FROM ride WHERE departure = 'Lyon' AND destination = 'Nice')),
('Pas de musique. La voiture est sale et les sièges déchirés !!!!', 1, 0, (SELECT id FROM user WHERE pseudo = 'roro'), (SELECT id FROM ride WHERE departure = 'Paris' AND destination = 'Lyon')),
('', 4, 0, (SELECT id FROM user WHERE pseudo = 'Pierre'), (SELECT id FROM ride WHERE departure = 'Nantes' AND destination = 'Paris' and departure_day ='20250615'));

