<?php

use Overblog\DataLoader\DataLoader;

return [
    'Book' => [
        'author' => function ($rootValue, $args, $context) {
            return DataLoader::await($context['loaders']['author']->load($rootValue['author_id']));
        }
    ],
    'Query' => [
        'getBooks' => function($root, $args, $context) {
            return $context['db']->fetchAll("SELECT * FROM book");
        }
    ]
];
