<?php

declare(strict_types=1);

/**
 * Date component — prints a formatted date.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/shared/date.php'; ?>
 *
 * Optional variables you can set before requiring:
 *   $dateFormat — PHP date() format string (default: 'Y')
 *   $dateValue  — any date string strtotime() understands (default: now)
 *
 * Examples:
 *   $dateFormat = 'd M Y';  →  31 Jul 2026
 *   $dateFormat = 'l, j F Y'; →  Friday, 31 July 2026
 *   $dateValue = '2026-07-31'; $dateFormat = 'Y'; → 2026
 *
 * @var string|null $dateFormat
 * @var string|null $dateValue
 */

echo date($dateFormat ?? 'Y', strtotime($dateValue ?? 'now'));
