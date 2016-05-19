<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler;

use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;
use Cron\CronExpression;

/**
 * Class Scheduler TODO: Write class description
 *
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class Scheduler extends Component
{

    public $taskClass = 'understeam\scheduler\DbTask';

    public $executor = 'understeam\scheduler\CallbackExecutor';

    /**
     * TODO
     * @param null|string $key
     * @param mixed $command
     * @param CronExpression|string $cronExpression
     * @param boolean $repeat
     * @return bool
     * @throws InvalidParamException
     */
    public function add($key, $command, $cronExpression, $repeat = true)
    {
        if (is_string($cronExpression)) {
            $cronExpression = CronExpression::factory($cronExpression);
        }
        if (!$cronExpression instanceof CronExpression) {
            throw new InvalidParamException("\$cronExpression must be a CronExpression object or valid cron string");
        }
        /** @var TaskInterface $taskClass */
        $taskClass = $this->taskClass;
        /** @var TaskInterface $task */
        $task = $taskClass::get($key);
        if ($task === null) {
            $task = new $taskClass();
        }
        $task->setKey($key);
        $task->setCommand($command);
        $task->setRepeat($repeat);
        $task->setExpression($cronExpression->getExpression());
        return $task->saveTask();
    }

    /**
     * TODO
     * @param $key
     * @return null|TaskInterface
     */
    public function get($key)
    {
        /** @var TaskInterface $taskClass */
        $taskClass = $this->taskClass;
        return $taskClass::get($key);
    }

    /**
     * @param $key
     * @param int $nth
     * @param string $currentTime
     * @return bool|\DateTime
     */
    public function getNextRunDate($key, $nth = 0, $currentTime = 'now')
    {
        $task = $this->get($key);
        if ($task === null) {
            return false;
        }
        $expression = CronExpression::factory($task->getExpression());
        if (!$expression instanceof CronExpression) {
            return false;
        }
        return $expression->getNextRunDate($currentTime, $nth);
    }

    /**
     * TODO
     * @param $key
     * @return boolean
     */
    public function has($key)
    {
        /** @var TaskInterface $taskClass */
        $taskClass = $this->taskClass;
        return $taskClass::has($key);
    }

    /**
     * TODO
     * @return \Iterator|TaskInterface[]
     */
    public function all()
    {
        /** @var TaskInterface $taskClass */
        $taskClass = $this->taskClass;
        return $taskClass::getAll();
    }

    /**
     * TODO
     * @param string $key
     * @return boolean
     */
    public function delete($key)
    {
        $task = $this->get($key);
        if ($task === null) {
            return false;
        }
        $task->deleteTask();
        return true;
    }

    /**
     * TODO
     * @param TaskInterface $task
     * @param string|\DateTime $time
     * @return boolean
     */
    public function handle(TaskInterface $task, $time = 'now')
    {
        $cronExpression = CronExpression::factory($task->getExpression());
        if ($cronExpression->isDue($time)) {
            $result = $this->execute($task->getCommand());
            if ($result && $task->getRepeat() == false) {
                $task->deleteTask();
            }
            return $result;
        }
        return false;
    }

    /**
     * TODO
     * @param mixed $command
     * @return boolean
     */
    public function execute($command)
    {
        return $this->getExecutor()->execute($command);
    }

    /**
     * TODO
     * @return ExecutorInterface
     */
    public function getExecutor()
    {
        if (!is_object($this->executor)) {
            $this->executor = Yii::createObject($this->executor);
        }
        return $this->executor;
    }
}
