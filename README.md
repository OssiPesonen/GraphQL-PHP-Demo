This is a demo on how to get GraphQL running with PHP with DataLoder installed to mitigate the infamous `n+1` problem. 

In this demo I wanted to demonstrate on how to actually set up GraphQL with PHP but also how to mitigate the infamous `n+1` problem by deferring the actual field resolution to a later stage. 

This project is set up on top of [Slim Framework](http://www.slimframework.com/) v4 which acts as an API with one single route.
Slim provides out-of-the-box Monolog support with their Skeleton project that you can use to actually log database queries and see what happens.

Database connection is handled using `Doctrine DBAL` and logged with an `SQLLogger` instance.

GraphQL implementation is provided by [webonyx/graphql-php](https://webonyx.github.io/graphql-php/)

## Instructions

- Download
- Unpack
- Set up a MySQL database table
    - (Any database with the same structure will do if you configure Doctrine DBAL to use the right driver)
- cd to project root and run:

`composer install`
    
- Run PHP app with:

`php -S localhost:8080 -t public public/index.php`

## Testing

After you app is running use Postman or any tool you like to send a GraphQL body request to your API which should now be running at `http://localhost:8080`

    query {
        getBooks {
            id
            title
            author {
                id
                name
            }
        }
    }
    

You can afterwards check your `logs/app.log` for the database queries that were executed.

#### Credits

I wanna credit [Ben Awad](https://www.youtube.com/channel/UC-8QAzbLcRglXeN_MY9blyw) whose demo project got me on the
 right track with using DataLoder to handle promises and setting up field resolvers.
