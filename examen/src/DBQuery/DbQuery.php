<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * SQL query builder.
 *
 * @since 1.5.0.1
 */
class DbQueryCore
{

    /**
     * Generates query and return SQL string.
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function build()
    {
        if ($this->query['type'] == 'SELECT') {
            $sql = 'SELECT ' . ((($this->query['select'])) ? implode(",\n", $this->query['select']) : '*') . "\n";
        } else {
            $sql = $this->query['type'] . ' ';
        }

        if (!$this->query['from']) {
            //throw new PrestaShopException('Table name not set in DbQuery object. Cannot build a valid SQL query.');
        }

        $sql .= 'FROM ' . implode(', ', $this->query['from']) . "\n";

        if ($this->query['join']) {
            $sql .= implode("\n", $this->query['join']) . "\n";
        }

        if ($this->query['where']) {
            $sql .= 'WHERE (' . implode(') AND (', $this->query['where']) . ")\n";
        }

        if ($this->query['group']) {
            $sql .= 'GROUP BY ' . implode(', ', $this->query['group']) . "\n";
        }

        if ($this->query['having']) {
            $sql .= 'HAVING (' . implode(') AND (', $this->query['having']) . ")\n";
        }

        if ($this->query['order']) {
            $sql .= 'ORDER BY ' . implode(', ', $this->query['order']) . "\n";
        }

        if ($this->query['limit']['limit']) {
            $limit = $this->query['limit'];
            $sql .= 'LIMIT ' . ($limit['offset'] ? $limit['offset'] . ', ' : '') . $limit['limit'];
        }

        return $sql;
    }

}
