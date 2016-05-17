<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class ScheduleController TODO: Write class description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class SchedulerController extends Controller
{

    public $scheduler = 'scheduler';

    public function actionIndex()
    {
        Console::output("Active tasks:");
        foreach ($this->getScheduler()->getActiveTasks() as $task) {
            Console::output("\t" . $task->getExpression() . ' ' . $task->getKey());
        }
    }

    public function actionCron()
    {
        $time = time();
        foreach ($this->getScheduler()->getActiveTasks() as $task) {
            if ($this->getScheduler()->handle($task, $time)) {
                Console::output("Executed " . $task->getKey());
            }
        }
    }

    /**
     * @return Scheduler
     */
    protected function getScheduler()
    {
        return Yii::$app->get($this->scheduler);
    }
}
