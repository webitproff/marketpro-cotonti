<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extensionService.getPublicPageUrl
[END_COT_EXT]
==================== */

/**
 * Market.
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 *
 * @var string $extensionCode
 * @var ?string $result
 */

declare(strict_types = 1);

// Market module has no public standalone page
if ($extensionCode === 'market') {
    $result = null;
}