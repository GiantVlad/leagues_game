<?php
namespace Classes;

/**
 * Class DbConnection Singleton
 * @package Classes
 */
class DbConnection
{
    /**
     * @var $msqli
     */
    private $msqli;

    /**
     * @var $instance
     */
    private static $instance;

    private function __construct()
    {}

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static;
            self::$instance->connect();
        }
        return self::$instance;
    }

    /**
     *
     */
    private function connect()
    {
        $this->user = getenv('DB_USER');
        $this->pass = getenv('DB_PASSWORD');
        $this->host = getenv('DB_HOST');
        $this->dbName = getenv('DB_NAME');
        $this->msqli = new \mysqli($this->host, $this->user, $this->pass, $this->dbName);
    }

    public function getConnect()
    {
        return $this->msqli;
    }

    /**
     * @param string $sql
     * @return array|mixed|void|null
     * @throws SystemException
     */
    public function query(string $sql)
    {
        $mysqlResult = $this->msqli->query($sql);

        if ($mysqlResult) {
            if ($mysqlResult instanceof \mysqli_result) {
                $result = [];
                while ($request_list_row = $mysqlResult->fetch_array(MYSQLI_ASSOC)) {
                    $result[] = $request_list_row;
                }
                if ($mysqlResult->num_rows === 0) {
                    return null;
                }
                if ($mysqlResult->num_rows === 1) {
                    return $result[0];
                }
                return $result;
            }
            return;
        }
        throw new SystemException($this->msqli->error);
    }

    /**
     * @param string $sql
     * @throws SystemException
     */
    public function multi_query(string $sql)
    {
        if ($this->msqli->multi_query($sql)) {
            $this->msqli->close();
            return;
        }
        throw new SystemException($this->msqli->error);
    }
}
