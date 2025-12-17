<?php
/**
 * Store item dictionary
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

declare(strict_types=1);

namespace cot\modules\market\inc;

defined('COT_CODE') or die('Wrong URL');

class MarketDictionary
{
    public const SOURCE_MARKET = 'market';

    /**
     * Published
     */
    public const STATE_PUBLISHED = 0;

    /**
     * Waiting for approve by admin (moderator)
     */
    public const STATE_PENDING = 1;

    /**
     * Draft
     */
    public const STATE_DRAFT = 2;
}