<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\interfaces;

use yii\web\IdentityInterface as YiiIdentityInterface;

interface IdentityInterface extends YiiIdentityInterface
{
    /**
     * Finds an identity by the given name.
     * @param string $name the name to be looked for
     * @return YiiIdentityInterface|null the identity object that matches the given name.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findName(string $name): ?YiiIdentityInterface;
}