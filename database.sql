CREATE DATABASE IF NOT EXISTS api_rest_laravel;
USE api_rest_laravel;

CREATE TABLE users(
    id             int(255) auto_increment NOT NULL,
    name           varchar(50) NOT NULL,
    surname        varchar(100) NOT NULL,
    role           varchar(20),
    email          varchar(255) NOT NULL,
    password       varchar(255) NOT NULL,
    description    text,
    image          varchar(255),
    create_at      datetime DEFAULT NULL, 
    update_at      datetime DEFAULT NULL, 
    remember_token varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id)
) ENGINE=InnoDb;

CREATE TABLE categies(
    id             int(255) auto_increment NOT NULL,
    name           varchar(100) NOT NULL,
    create_at      datetime DEFAULT NULL, 
    update_at      datetime DEFAULT NULL, 
    CONSTRAINT pk_categories PRIMARY KEY(id)

) ENGINE=InnoDb;

CREATE TABLE posts(
    id             int(255) auto_increment NOT NULL,
    user_id        int(255) NOT NULL,
    categorie_id   int(255) auto_increment NOT NULL,
    title          varchar(255) NOT NULL,
    content        text NOT NULL,
    image          VARCHAR(255),
    create_at      datetime DEFAULT NULL, 
    update_at      datetime DEFAULT NULL,
    CONSTRAINT pk_posts PRIMARY KEY(id),
    CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES users(id),
    CONSTRAINT fk_post_category FOREIGN KEY(categorie_id) REFERENCES categies(id)
) ENGINE=InnoDb;