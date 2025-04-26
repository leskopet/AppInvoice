-- test data

INSERT INTO `AppInvoice`.`Device` (`id`) VALUES 
    ('2b6a34efeb27a2d5dd3f9bdcf8bf2766871da7c6b413d7bc282b1ac3c5bf650e'),
    ('2b6a34efeb27a2d5dd3f9bdcf8bf2766871da7c6b413d7bc282b1ac3c5bf650f'),
    ('2b6a34efeb27a2d5dd3f9bdcf8bf2766871da7c6b413d7bc282b1ac3c5bf6510');

INSERT INTO `AppInvoice`.`Address` (`id`, `state`, `region`, `city`, `street`, `street_number`, `postal_code`) VALUES
    (1, 'Slovensko', NULL, 'Bratislava', 'Hlavná', '1', '82101'),
    (2, 'Slovensko', NULL, 'Žilina', 'Vysokoškolákov', '1', '82101'),
    (3, 'Slovensko', NULL, 'Košice', 'Podhradová', '1', '82101'),
    (4, 'Slovensko', NULL, 'Trnava', 'Hlavná', '1', '82101'),
    (5, 'Slovensko', NULL, 'Trenčín', 'Hlavná', '1', '82101'),
    (6, 'Slovensko', NULL, 'Poprad', 'Hlavná', '1', '82101'),
    (7, 'Slovensko', NULL, 'Martin', 'Hlavná', '1', '82101');

INSERT INTO `AppInvoice`.`User` (`id`, `fname`, `lname`, `email`, `phone`, `description`, `address`, `role`, `tag`, `password`) VALUES
    (1, 'Admin', 'Admin', 'admin@appinvoice.sk', '+421123456789', '', 2, '9', '0', '$2y$10$uxc2/nFd86hZhWgtZOJnB.TumsP96M.ObquKLXkCtHbItGhcvA1Qm'),         -- password: admin
    (2, 'User', 'User', 'user@appinvoice.sk', '+421123456789', '', 1, '0', '0', '$2y$10$3It6i.h6o.gvcdQCjuPOdOq3kDle11gSxlQFuniJ61e/l5b4IXKV6'),            -- password: password
    (3, 'Pouzivatel', '1', 'pouzivatel@appinvoice.sk', '+421123456789', '', 3, '0', '0', '$2y$10$ptuTUbF5pa1ohJiLxWjJhuKv7El5p4Fsdqls6p6J.wtHHoQRFs5cK'),   -- password: heslo
    (4, 'Suplier', '1', 'suplier@appinvoice.sk', '+421123456789', '', 1, '0', '0', '$2y$10$APNvhwG0IAmOpzey3wT18e0EeEa1JW8WqA5mlr2hZVrauysw8QOaS');         -- password: password

INSERT INTO `AppInvoice`.`Auto_login` (`id`, `user`, `device`) VALUES
    (1, 2, '2b6a34efeb27a2d5dd3f9bdcf8bf2766871da7c6b413d7bc282b1ac3c5bf650e'),
    (2, 2, '2b6a34efeb27a2d5dd3f9bdcf8bf2766871da7c6b413d7bc282b1ac3c5bf650f'),
    (3, 3, '2b6a34efeb27a2d5dd3f9bdcf8bf2766871da7c6b413d7bc282b1ac3c5bf6510');

INSERT INTO `AppInvoice`.`Company` (`id`, `user`, `title`, `description`, `email`, `phone`, `ico`, `dic`, `icdph`, `iban`, `swift`, `bank`, `address`) VALUES
    (1, 1, 'AppInvoice', 'Osoba zapísaná v Živnostenskom registri pod číslom 750-62386, vydal Okresný úrad Prešov dňa 9.9.2022.', 'admin@appinvoice.sk', '+421123456789', '12345678', '1234567890', 'SK1234567890', 'SK83 0900 0000 0051 3514 6499', 'GISKBX', 'Slovenská sporiteľňa, a.s.', 2),
    (2, 2, 'User', 'Osoba zapísaná v Živnostenskom registri pod číslom 750-62386, vydal Okresný úrad Prešov dňa 9.9.2022.', 'user@appinvoice.sk', '+421123456789', '12345678', '1234567890', 'SK1234567890', 'SK83 0900 0000 0051 3514 6499', 'GISKBX', 'Slovenská sporiteľňa, a.s.', 1),
    (3, 3, 'Pouzivatel', '', 'pouzivatel@appinvoice.sk', '+421123456789', '12345678', '1234567890', 'SK1234567890', 'SK83 0900 0000 0051 3514 6499', 'GISKBX', 'Slovenská sporiteľňa, a.s.', 3),
    (4, 4, 'Suplier', '', 'suplier@appinvoice.sk', '+421123456789', '12345678', '1234567890', 'SK1234567890', 'SK83 0900 0000 0051 3514 6499', 'GISKBX', 'Slovenská sporiteľňa, a.s.', 1),
    (5, NULL, 'Customer', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 4),
    (6, NULL, 'Customer2', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 5),
    (7, NULL, 'Customer3', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 6),
    (8, NULL, 'Customer4', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 7),
    (9, NULL, 'Customer5', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 4),
    (10, NULL, 'Customer6', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 1),
    (11, NULL, 'Customer7', '', '', '', '87654321', '9876543210', 'SK9876543210', '', '', '', 3);

INSERT INTO `AppInvoice`.`Invoice` (`id`, `suplier`, `customer`, `title`, `description`, `total`, `vat`, `total_vat`, `status`, `created`, `suplied`, `due_date`) VALUES
    (1,  1, 5,  '2025001', '', 1000, 230, 1230, 2, '2025-01-01', '2025-01-01', '2025-01-15'),
    (2,  1, 6,  '2025002', '', 100,  23,  123,  2, '2025-02-04', '2025-02-04', '2025-02-18'),
    (3,  1, 7,  '2025003', '', 1000, 230, 1230, 3, '2025-03-01', '2025-03-01', '2025-03-15'),
    (4,  1, 8,  '2025004', '', 100,  23,  123,  1, '2025-04-30', '2025-04-30', '2025-05-14'),
    (5,  1, 9,  '2025005', '', 1000, 230, 1230, 0, '2025-05-02', '2025-05-02', '2025-05-16'),
    (6,  2, 10, '2025001', '', 100,  23,  123,  2, '2025-01-06', '2025-01-06', '2025-01-20'),
    (7,  2, 11, '2025002', '', 1000, 230, 1230, 2, '2025-02-05', '2025-02-05', '2025-02-19'),
    (8,  2, 5,  '2025003', '', 100,  23,  123,  2, '2025-03-01', '2025-03-01', '2025-03-15'),
    (9,  2, 6,  '2025004', '', 1000, 230, 1230, 2, '2025-04-04', '2025-04-04', '2025-04-18'),
    (10, 3, 7,  '2025001', '', 100,  23,  123,  2, '2025-01-01', '2025-01-01', '2025-01-15'),
    (11, 3, 8,  '2025002', '', 1000, 230, 1230, 2, '2025-03-03', '2025-03-03', '2025-03-17'),
    (12, 3, 9,  '2025003', '', 100,  23,  123,  1, '2025-04-02', '2025-04-02', '2025-04-16'),
    (13, 4, 10, '2025001', '', 1000, 230, 1230, 2, '2025-02-06', '2025-02-06', '2025-02-20'),
    (14, 4, 11, '2025002', '', 100,  23,  123,  1, '2025-04-05', '2025-04-05', '2025-04-19');

INSERT INTO `AppInvoice`.`InvoiceItem` (`id`, `invoice`, `title`, `description`, `quantity`, `unit`, `price`) VALUES
    (1,  1,   'Item1', '', 3,   'kg',  100),
    (2,  1,   'Item2', '', 200, 'm',   2),
    (3,  1,   'Item3', '', 3,   'kg',  100),
    (4,  2,   'Item1', '', 2,   'hod', 50),
    (5,  3,   'Item1', '', 10,  'ks',  50),
    (6,  3,   'Item2', '', 5,   'kg',  100),
    (7,  4,   'Item1', '', 20,  'm',   1),
    (8,  4,   'Item2', '', 4,   'ks',  20),
    (9,  5,   'Item1', '', 25,  'hod', 40),
    (10, 6,   'Item1', '', 10,  'm',   2),
    (11, 6,   'Item2', '', 2,   'ks',  40),
    (12, 7,   'Item1', '', 20,  'ks',  25),
    (13, 7,   'Item2', '', 4,   'kg',  125),
    (14, 8,   'Item1', '', 1,   'hod', 100),
    (15, 9,   'Item1', '', 5,   'm',   20),
    (16, 9,   'Item2', '', 10,  'ks',  40),
    (17, 9,   'Item3', '', 5,   'kg',  100),
    (18, 10,  'Item1', '', 2,   'hod', 50),
    (19, 11,  'Item1', '', 10,  'ks',  50),
    (20, 11,  'Item2', '', 20,  'kg',  25),
    (21, 12,  'Item1', '', 20,  'm',   2),
    (22, 12,  'Item2', '', 3,   'ks',  20),
    (23, 13,  'Item1', '', 40,  'hod', 25),
    (24, 14,  'Item1', '', 20,  'm',   1),
    (25, 14,  'Item2', '', 2,   'ks',  40);