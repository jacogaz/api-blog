CREATE DATABASE IF NOT EXISTS blog_ctm;
USE blog_ctm;

CREATE TABLE users(
id              int(255) auto_increment not null,
role            varchar(20),
email           varchar(255) NOT NULL,
password        varchar(255) NOT NULL,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
remember_token  varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE categories(
id              int(255) auto_increment not null,
name            varchar(100),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE posts(
id              int(255) auto_increment not null,
user_id         int(255) not null,
category_id     int(255),
title           varchar(255) not null,
content         text not null,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT pk_posts PRIMARY KEY(id),
CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_post_category FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDb;

INSERT INTO `users` (`id`, `role`, `email`, `password`, `image`, `created_at`, `updated_at`, `remember_token`) VALUES (NULL, 'ROLE_ADMIN', 'admin@admin.com', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', NULL, '2019-06-06 00:00:00', '2019-06-06 00:00:00', NULL);