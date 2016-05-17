<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler;

use Yii;
use yii\base\Component;

/**
 * Class CommandBusExecutor TODO: Write class description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class CommandBusExecutor extends Component implements ExecutorInterface
{

    public $commandBus = 'commandBus';

    /**
     * @param mixed $command
     * @return boolean
     */
    public function execute($command)
    {
        $this->getCommandBus()->handle($command);
        return true;
    }

    /**
     * @return \trntv\bus\CommandBus
     * @throws \yii\base\InvalidConfigException
     */
    protected function getCommandBus()
    {
        return Yii::$app->get($this->commandBus);
    }
}
