<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler\tests\commands;
use trntv\bus\interfaces\SelfHandlingCommand;

/**
 * Class TestCommand TODO: Write class description
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class EchoCommand implements SelfHandlingCommand
{

    public $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function handle($command)
    {
        echo $this->string;
    }
}
