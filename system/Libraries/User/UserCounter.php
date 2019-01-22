<?php
/**
 * 日计数器，每日相关的计数器
 */

namespace Amber\System\Libraries\User;

use Amber\System\Libraries\AbstractDayCounter;

class UserCounter extends AbstractDayCounter
{
    const SMS_CODE              = 1; //手机验证码
    const MILLION_WINNER_START  = 2; //百万赢家下发问题
    const KEY_PREFIX     = 'user:day:counter:';
}
