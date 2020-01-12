<?php

return [
    'Book'  => [
        'author' => function ($book, $args, $context) {
            return $context['loaders']['author']->load($book['author_id']);
        }
    ],
    'Query' => [
        'getBooks' => function ($root, $args, $context) {
            $books = $context['db']->fetchAll("SELECT * FROM book");
            return $books;
        }
    ]
];
