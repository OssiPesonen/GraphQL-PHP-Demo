This is a repository demonstrating  how to actually set up GraphQL with PHP but also how to mitigate the infamous `n+1` 
problem by deferring the actual field resolution to a later stage. This implementation uses [DataloaderPHP](https://www.google.com/search?client=firefox-b-d&q=dataloader-php)
for the dererral.

The project is set up on top of [Slim Framework](http://www.slimframework.com/) v4 which acts as an API.
Slim provides out-of-the-box Monolog support with their Skeleton project that you can use to actually log database queries and see what happens.

Database connection is handled using [Doctrine DBAL](https://github.com/doctrine/dbal) and logged with an `SQLLogger` instance.

GraphQL implementation is provided by [webonyx/graphql-php](https://webonyx.github.io/graphql-php/)

## Instructions

- Download
- Unpack
- Set up a database table (I used MySQL here) and import the `database.sql`    
- Add your database connection settings to app/settings.php `db` section. Defaults are set to localhost demo database.
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
    

You can afterwards check your `logs/app.log` for the database queries that were executed. The log entry should look like this:

    {
      "1": {
        "sql": "SELECT * FROM book",
        "params": [],
        "types": [],
        "executionMS": 0.0005371570587158203
      },
      "2": {
        "sql": "SELECT id, name FROM author WHERE id in (?)",
        "params": [
          "1,2,3"
        ],
        "types": [],
        "executionMS": 0.0009009838104248047
      }
    }
    
So we fetched the books in one query and after getting all the possible author ids for all the books, we queried them in one go.

#### Credits

I wanna credit [Ben Awad](https://www.youtube.com/channel/UC-8QAzbLcRglXeN_MY9blyw) whose demo project got me on the
 right track with using DataLoder to handle promises and setting up field resolvers.
