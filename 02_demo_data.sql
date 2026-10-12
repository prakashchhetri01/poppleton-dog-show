-- Fictional demo data added for the GitHub edition.
USE poppleton;
INSERT INTO owners VALUES (1, 'Demo Owner One', 'owner1@example.com', '00000000001', '1 Example Street'), (2, 'Demo Owner Two', 'owner2@example.com', '00000000002', '2 Example Street');
INSERT INTO breeds VALUES (1, 'Labrador'), (2, 'Beagle');
INSERT INTO dogs VALUES (1, 'Demo Buddy', 1, 1), (2, 'Demo Bella', 2, 2), (3, 'Demo Max', 1, 1);
INSERT INTO events VALUES (1, 'Demo Spring Show', '2025-04-01'), (2, 'Demo Autumn Show', '2025-10-01');
INSERT INTO competitions VALUES (1, 1, 'Demo Obedience'), (2, 2, 'Demo Agility');
INSERT INTO entries (dog_id, competition_id, score) VALUES (1, 1, 80), (1, 2, 90), (2, 1, 90), (2, 2, 94), (3, 1, 95);
