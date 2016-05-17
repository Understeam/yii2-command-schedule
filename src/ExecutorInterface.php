<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler;

/**
 * Interface ExecutorInterface TODO: Write interface description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
interface ExecutorInterface
{

    /**
     * @param mixed $command
     * @return boolean
     */
    public function execute($command);

}
