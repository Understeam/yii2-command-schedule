<?php

namespace understeam\scheduler\tests\unit;

use Codeception\TestCase\Test;
use understeam\scheduler\DbTask;
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
        $this->getScheduler()->schedule('test', 'command', '* * * * *');
        expect($this->getScheduler()->findTaskByKey('test'))->notNull();
    }

    public function testTaskDisable()
    {
        $this->getScheduler()->schedule('test', 'command', '* * * * *');
        $this->getScheduler()->disableTask($this->getScheduler()->findTaskByKey('test'));
        expect($this->getScheduler()->findTaskByKey('test'))->null();
        expect($this->getScheduler()->findTaskByKey('test', Scheduler::STATUS_DISABLED))->notNull();
    }

    public function testTaskReplace()
    {
        $this->getScheduler()->schedule('test', 'command1', '0 * * * *');
        $task = $this->getScheduler()->findTaskByKey('test');
        expect($task->getCommand())->equals('command1');
        expect($task->getExpression())->equals('0 * * * *');

        $this->getScheduler()->schedule('test', 'command2', '* * * * *');
        $task = $this->getScheduler()->findTaskByKey('test');
        expect($task->getCommand())->equals('command2');
        expect($task->getExpression())->equals('* * * * *');
        $this->getScheduler()->disableTask($task);
    }

    public function testTaskIteration()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->getScheduler()->schedule("task{$i}", "command{$i}", '* * * * *');
        }
        $tasks = $this->getScheduler()->getActiveTasks();
        $count = 0;
        foreach ($tasks as $task) {
            $count++;
            expect($task->getStatus())->equals(Scheduler::STATUS_ACTIVE);
        }
        expect($count)->equals(3);
    }

    public function testTaskExecute()
    {
        $command = new EchoCommand('test1');
        $task = new DbTask();
        $task->setCommand($command);
        ob_start();
        $this->getScheduler()->execute($task->getCommand());
        $result = ob_get_clean();
        expect($result)->equals('test1');
    }

    public function testTaskHandling()
    {
        $tasks = $this->getScheduler()->getActiveTasks();
        foreach ($tasks as $task) {
            $this->getScheduler()->disableTask($task);
        }
        $time = '2016-05-12 01:00:00';
        $this->getScheduler()->schedule('test0', new EchoCommand('0'), '* * * * *'); // +
        $this->getScheduler()->schedule('test1', new EchoCommand('1'), '0 * * * *'); // +
        $this->getScheduler()->schedule('test2', new EchoCommand('2'), '0 0 * * *'); // -
        $this->getScheduler()->schedule('test3', new EchoCommand('3'), '0 1 * * *'); // +
        $this->getScheduler()->schedule('test4', new EchoCommand('4'), '0 1 12 * *');// +
        $this->getScheduler()->schedule('test5', new EchoCommand('5'), '0 1 * 5 *'); // +
        $tasks = $this->getScheduler()->getActiveTasks();
        ob_start();
        foreach ($tasks as $task) {
            $this->getScheduler()->handle($task, $time);
        }
        $result = ob_get_clean();
        expect($result)->equals('01345');
    }

    /**
     * @return Scheduler
     */
    protected function getScheduler()
    {
        return Yii::$app->get('scheduler');
    }

}
