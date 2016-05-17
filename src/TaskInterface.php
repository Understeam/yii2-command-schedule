<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler;

/**
 * Interface TaskInterface TODO: Write interface description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
interface TaskInterface
{

    /**
     * @param string $key
     * @param integer $status
     * @return TaskInterface|null
     */
    public static function findByKey($key, $status = null);

    /**
     * @return boolean
     */
    public function saveTask();

    /**
     * @return mixed
     */
    public function getCommand();

    /**
     * @param mixed $command
     */
    public function setCommand($command);

    /**
     * @return string
     */
    public function getExpression();

    /**
     * @param string $expression
     */
    public function setExpression($expression);

    /**
     * @return integer
     */
    public function getStatus();

    /**
     * @param integer $status
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return \Iterator|TaskInterface[]
     */
    public static function getActiveTasks();

}
