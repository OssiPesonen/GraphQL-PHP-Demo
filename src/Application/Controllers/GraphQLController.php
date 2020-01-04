<?php
declare(strict_types=1);

namespace App\Application\Controllers;

use Doctrine\DBAL\Connection;
use GraphQL\Executor\Executor;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Utils\BuildSchema;
use Overblog\DataLoader\DataLoader;
use Overblog\DataLoader\Promise\Adapter\Webonyx\GraphQL\SyncPromiseAdapter;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class GraphQLController
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->db = $connection;
        $this->logger = $logger;
    }

    public function index(Request $request, Response $response)
    {
        $graphQLSyncPromiseAdapter = new SyncPromiseAdapter();
        $promiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($graphQLSyncPromiseAdapter);

        # Warning! As an example, this resides in the controller method. However, a smarter way of handling loaders should be made because this will bloat up the function
        $authorLoader = new DataLoader(function ($keys) use ($promiseAdapter) {
            $ids = join(',', $keys);
            $idMap = array_flip($keys);

            $rows = $this->db->executeQuery("SELECT id, name FROM author WHERE id in (?)", [$ids]);

            foreach ($rows as $r) {
                $idMap[$r['id']] = $r;
            }

            return $promiseAdapter->createAll(array_values($idMap));
        }, $promiseAdapter);

        GraphQL::setPromiseAdapter($graphQLSyncPromiseAdapter);

        $this->resolvers(include dirname(__DIR__, 3) . '/src/GraphQL/resolvers.php');
        $schema = BuildSchema::build(file_get_contents(dirname(__DIR__, 3) . '/src/GraphQL/schema.graphqls'));

        # Request
        $input = json_decode(file_get_contents('php://input'), true);
        $query = $input['query'];

        # Variables
        $variableValues = isset($input['variables']) ? $input['variables'] : null;

        # Context, objects and data the resolver can then access. In this case the database object.
        $context = [
            'authorLoader' => $authorLoader,
            'db'           => $this->db,
            'logger'       => $this->logger
        ];

        # Resolver result
        $result = GraphQL::executeQuery($schema, $query, null, $context, $variableValues);

        $payload = json_encode($result);
        $response->getBody()->write($payload);

        $sqlQueryLogger = $this->db->getConfiguration()->getSQLLogger();

        $this->logger->info(json_encode($sqlQueryLogger->queries));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function resolvers($resolvers)
    {
        Executor::setDefaultFieldResolver(function ($source, $args, $context, ResolveInfo $info) use ($resolvers) {
            $fieldName = $info->fieldName;

            if (is_null($fieldName)) {
                throw new \Exception('Could not get $fieldName from ResolveInfo');
            }

            if (is_null($info->parentType)) {
                throw new \Exception('Could not get $parentType from ResolveInfo');
            }

            $parentTypeName = $info->parentType->name;

            if (isset($resolvers[$parentTypeName])) {
                $resolver = $resolvers[$parentTypeName];

                if (is_array($resolver)) {
                    if (array_key_exists($fieldName, $resolver)) {
                        $value = $resolver[$fieldName];

                        return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                    }
                }

                if (is_object($resolver)) {
                    if (isset($resolver->{$fieldName})) {
                        $value = $resolver->{$fieldName};

                        return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                    }
                }
            }

            return Executor::defaultFieldResolver($source, $args, $context, $info);
        });
    }
}
