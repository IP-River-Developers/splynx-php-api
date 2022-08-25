<?php
// phpcs:disable PSR1.Classes.ClassDeclaration

use yii\base\Exception;

class SplynxApiCustomExceptions extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function invalidApiDomain()
    {
        return 'Warning: Config has unknown API domain, please check your system API settings.';
    }
}
