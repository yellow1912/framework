<?php
/**
 * Created by Rubikin Team.
 * Date: 3/26/13
 * Time: 5:16 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Exceptions;


class PaymentException extends \Exception {
    const INVALID_REQUEST  = 1;
    const INVALID_RESPONSE = 2;
    const AMOUNT_MISMATCH  = 3;
    const EMAIL_MISMATCH   = 4;
    const CURL_EXCEPTION   = 5;
}