<?php

namespace understeam\scheduler\tests\unit;

use Codeception\TestCase\Test;
use understeam\scheduler\Scheduler;
use understeam\scheduler\tests\commands\EchoCommand;
use Yii;

class SchedulerTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected static function runConsoleAction($route, $params = [])
    {
        Yii::$app->runAction($route, $params);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::runConsoleAction('migrate/up', ['interactive' => false]);
    }

    public static function tearDownAfterClass()
    {
        static::runConsoleAction('migrate/down', ['interactive' => false]);
        parent::tearDownAfterClass();
    }

    public function testTaskCreation()
    {
        $this->getScheduler()->add('test', 'command', '* * * * *');
        expect($this->getScheduler()->get('test'))->notNull();
    }

    public function testTaskDelete()
    {
        $this->getScheduler()->add('test', 'command', '* * * * *');
        expect($this->getScheduler()->get('test'))->notNull();
        $this->getScheduler()->delete('test');
        expect($this->getScheduler()->get('test'))->null();
    }

    public function testTaskReplace()
    {
        $this->getScheduler()->add('test', 'command1', '0 * * * *');
        $task = $this->getScheduler()->get('test');
        expect($task->getCommand())->equals('command1');
        expect($task->getExpression())->equals('0 * * * *');

        $this->getScheduler()->add('test', 'command2', '* * * * *');
        $task = $this->getScheduler()->get('test');
        expect($task->getCommand())->equals('command2');
        expect($task->getExpression())->equals('* * * * *');
        $task->deleteTask();
    }

    public function testTaskIteration()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->getScheduler()->add("task{$i}", "command{$i}", '* * * * *');
        }
        expect($this->getTaskCount())->equals(3);
    }

    public function testTaskExecute()
    {
        ob_start();
        $this->getScheduler()->execute(new EchoCommand('test1'));
        $result = ob_get_clean();
        expect($result)->equals('test1');
    }

    public function testTaskHandling()
    {
        $this->clearTasks();
        $this->getScheduler()->add('test0', new EchoCommand('0'), '* * * * *'); // +
        $this->getScheduler()->add('test1', new EchoCommand('1'), '0 * * * *'); // +
        $this->getScheduler()->add('test2', new EchoCommand('2'), '0 0 * * *'); // -
        $this->getScheduler()->add('test3', new EchoCommand('3'), '0 1 * * *'); // +
        $this->getScheduler()->add('test4', new EchoCommand('4'), '0 1 12 * *');// +
        $this->getScheduler()->add('test5', new EchoCommand('5'), '0 1 * 5 *'); // +
        $result = $this->handleAllTasks('2016-05-12 01:00:00');
        expect($result)->equals('01345');
    }

    public function testOneTimeTask()
    {
        $this->clearTasks();
        $this->getScheduler()->add('one-time-task', new EchoCommand('one'), '* * * * *', false);
        $result = $this->handleAllTasks();
        expect($result)->equals('one');
        expect($this->getTaskCount())->equals(0);
        expect($this->getScheduler()->get('one-time-task'))->null();
    }

    public function testNextRunDate()
    {
        $this->clearTasks();
        $date = '2016-05-12 01:00:01';
        $this->getScheduler()->add('every-hour', new EchoCommand('one'), '0 * * * *');
        $nextDate = $this->getScheduler()->getNextRunDate('every-hour', 0, $date)->format('Y-m-d H:i:s');
        expect($nextDate)->equals('2016-05-12 02:00:00');
    }

    private function handleAllTasks($time = 'now')
    {
        $tasks = $this->getScheduler()->all();
        ob_start();
        foreach ($tasks as $task) {
            $this->getScheduler()->handle($task, $time);
        }
        return ob_get_clean();
    }

    private function clearTasks()
    {
        $tasks = $this->getScheduler()->all();
        foreach ($tasks as $task) {
            $task->deleteTask();
        }
    }

    private function getTaskCount()
    {
        $tasks = $this->getScheduler()->all();
        $count = 0;
        // Cannot use count() because of \Iterator usage
        foreach ($tasks as $task) {
            $count++;
        }
        return $count;
    }

    /**
     * @return Scheduler
     */
    protected function getScheduler()
    {
        return Yii::$app->get('scheduler');
    }

}
