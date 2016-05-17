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
 * TODO: One-time tasks support: disable task after one execution
 *
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class Scheduler extends Component
{

    public $taskClass = 'understeam\scheduler\DbTask';

    public $executor = 'understeam\scheduler\CallbackExecutor';

    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * Schedule an unique task
     * @param null|string $key
     * @param mixed $command
     * @param CronExpression|string $cronExpression
     * @return bool
     * @throws InvalidParamException
     */
    public function schedule($key, $command, $cronExpression)
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
        $task = $taskClass::findByKey($key, self::STATUS_ACTIVE);
        if ($task === null) {
            $task = new $taskClass();
            $task->setStatus(self::STATUS_ACTIVE);
        }
        $task->setKey($key);
        $task->setCommand($command);
        $task->setExpression($cronExpression->getExpression());
        return $task->saveTask();
    }

    public function disableTask(TaskInterface $task)
    {
        $task->setStatus(self::STATUS_DISABLED);
        return $task->saveTask();
    }

    /**
     * @return \Iterator|TaskInterface[]
     */
    public function getActiveTasks()
    {
        /** @var TaskInterface $taskClass */
        $taskClass = $this->taskClass;
        return $taskClass::getActiveTasks();
    }

    /**
     * @param TaskInterface $task
     * @param string|\DateTime $time
     * @return boolean
     */
    public function handle(TaskInterface $task, $time = 'now')
    {
        $cronExpression = CronExpression::factory($task->getExpression());
        if ($cronExpression->isDue($time)) {
            return $this->execute($task->getCommand());
        }
        return false;
    }

    /**
     * @param mixed $command
     * @return boolean
     */
    public function execute($command)
    {
        return $this->getExecutor()->execute($command);
    }

    /**
     * @return ExecutorInterface
     */
    public function getExecutor()
    {
        if (!is_object($this->executor)) {
            $this->executor = Yii::createObject($this->executor);
        }
        return $this->executor;
    }

    public function findTaskByKey($key, $status = self::STATUS_ACTIVE)
    {
        /** @var TaskInterface $taskClass */
        $taskClass = $this->taskClass;
        return $taskClass::findByKey($key, $status);
    }
}
