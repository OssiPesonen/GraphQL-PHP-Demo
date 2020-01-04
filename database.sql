CREATE TABLE IF NOT EXISTS `author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8_swedish_ci  NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

INSERT INTO `author` (`id`, `name`) VALUES
(1, 'J. K. Rowling'),
(2, 'Andrzej Sapkowski'),
(3, 'J. R. R. Tolkien');

CREATE TABLE IF NOT EXISTS `book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8_swedish_ci  NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

INSERT INTO `book` (`id`, `title`, `author_id`) VALUES
(1, 'Harry Potter and the Philosopher\'s Stone', 1),
(2, 'Harry Potter and the Chamber of Secrets', 1),
(3, 'Harry Potter and the Prizoner of Azkaban', 1),
(4, 'Harry Potter and the Goblet of Fire', 1),
(5, 'Harry Potter and the Order of the Phoenix', 1),
(6, 'Harry Potter and the Half-Blood Prince', 1),
(7, 'Harry Potter and the Deathly Hallows', 1),
(8, 'The Last Wish', 2),
(9, 'Sword of Destiny', 2),
(10, 'Blood of Elves', 2),
(11, 'Time of Contempt', 2),
(12, 'Baptism of Fire', 2),
(13, 'The Tower of the Swallow', 2),
(14, 'The Lady of the Lake', 2),
(15, 'Season of the Storms', 2),
(16, 'Lord of the Rings', 3),
(17, 'Silmarillion', 3),
(18, 'The Hobbit', 3);
