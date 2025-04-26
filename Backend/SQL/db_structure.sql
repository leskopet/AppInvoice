
CREATE DATABASE AppInvoice;

CREATE TABLE AppInvoice.Device (
    id VARCHAR(255) PRIMARY KEY NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE AppInvoice.Address (
    id SERIAL PRIMARY KEY,
    state VARCHAR(255),
    region VARCHAR(255),
    city VARCHAR(255),
    street VARCHAR(255),
    street_number VARCHAR(255),
    postal_code VARCHAR(255)
);

CREATE TABLE AppInvoice.User (
    id SERIAL PRIMARY KEY,
    fname VARCHAR(255) NOT NULL,
    lname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    address BIGINT UNSIGNED,
    role INTEGER NOT NULL DEFAULT 0,
    tag INTEGER NOT NULL DEFAULT 0,
    created_DT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_signin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    password VARCHAR(255),
    is_verified BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (address) references Address(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE AppInvoice.Auto_login (
    id SERIAL PRIMARY KEY,
    user BIGINT UNSIGNED NOT NULL,
    device VARCHAR(255) NOT NULL,
    FOREIGN KEY (user) references AppInvoice.User(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (device) references Device(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE AppInvoice.Company (
    id SERIAL PRIMARY KEY,
    user BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    email VARCHAR(255) NOT NULL DEFAULT '',
    phone VARCHAR(255) NOT NULL DEFAULT '',
    ico CHAR(8) NOT NULL,
    dic CHAR(10) NOT NULL,
    icdph CHAR(12) NULL,
    iban VARCHAR(255) NULL,
    swift CHAR(8) NULL,
    bank VARCHAR(255) NULL,
    address BIGINT UNSIGNED,
    FOREIGN KEY (user) references AppInvoice.User(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (address) references AppInvoice.Address(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE AppInvoice.Invoice (
    id SERIAL PRIMARY KEY,
    suplier BIGINT UNSIGNED NOT NULL,
    customer BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    total DOUBLE NOT NULL,
    vat DOUBLE NOT NULL,
    total_vat DOUBLE NOT NULL,
    status INTEGER NOT NULL DEFAULT 0,
        -- 0 - open
        -- 1 - sent / unpaid
        -- 2 - paid
        -- 3 - overdue
    created DATE DEFAULT CURRENT_TIMESTAMP,
    suplied DATE DEFAULT CURRENT_TIMESTAMP,
    due_date DATE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (suplier) references AppInvoice.Company(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer) references AppInvoice.Company(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE AppInvoice.Invoice_item (
    id SERIAL PRIMARY KEY,
    invoice BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    unit VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice) references AppInvoice.Invoice(id) ON DELETE CASCADE ON UPDATE CASCADE
);
