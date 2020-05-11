<?php

namespace Classes;

/**
 * Interface BaseModel
 * @package Classes
 */
interface BaseModel
{
    /**
     * BaseModel constructor.
     * @param DbConnection $db
     */
    public function __construct(DbConnection $db);
}
