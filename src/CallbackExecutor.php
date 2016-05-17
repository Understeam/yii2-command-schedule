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
 * Class CallbackExecutor TODO: Write class description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class CallbackExecutor extends Component implements ExecutorInterface
{

    /**
     * @param mixed $command
     * @return boolean
     */
    public function execute($command)
    {
        if (is_callable($command)) {
            return call_user_func($command);
        }
        Yii::warning("Cannot execute callable command: " . serialize($command), 'scheduler');
        return false;
    }
}
