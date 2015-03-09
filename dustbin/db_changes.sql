ALTER TABLE `users` ADD `first_name` VARCHAR(25) NOT NULL AFTER `id`;

ALTER TABLE `users` ADD `last_name` VARCHAR(25) NOT NULL AFTER `first_name`;