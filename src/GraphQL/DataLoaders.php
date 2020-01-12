<?php
namespace App\GraphQL;
use Overblog\DataLoader\DataLoader;
use Doctrine\DBAL\Connection;

class DataLoaders
{
    protected $db;
    public function __construct(Connection $connection)
    {
        $this->db = $connection;
    }
    /**
     * GraphQL DataLoaders which get injected into the context for resolvers to use
     *
     * @param $promiseAdapter
     * @return array
     */
    public function build($promiseAdapter)
    {
        return [
            'author' => new DataLoader(function ($authorIds) use ($promiseAdapter) {
                    $map = [];

                    $query = $this->db->executeQuery("SELECT id, `name` FROM author WHERE id in (?)",
                        [$authorIds],
                        [Connection::PARAM_INT_ARRAY]
                    );
                    $rows = $query->fetchAll();

                    foreach ($rows as $r) {
                        $map[$r['id']] = $r;
                    }

                    return $promiseAdapter->createAll(array_values($map));
                }, $promiseAdapter)
        ];
    }
}
