<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Analytic;

use Hiject\Core\Base;
use Hiject\Model\TaskModel;

/**
 * Estimated/Spent Time Comparison
 */
class EstimatedTimeComparisonAnalytic extends Base
{
    /**
     * Build report
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function build($project_id)
    {
        $rows = $this->db->table(TaskModel::TABLE)
            ->columns('SUM(time_estimated) AS time_estimated', 'SUM(time_spent) AS time_spent', 'is_active')
            ->eq('project_id', $project_id)
            ->groupBy('is_active')
            ->findAll();

        $metrics = array(
            'open' => array(
                'time_spent' => 0,
                'time_estimated' => 0,
            ),
            'closed' => array(
                'time_spent' => 0,
                'time_estimated' => 0,
            ),
        );

        foreach ($rows as $row) {
            $key = $row['is_active'] == TaskModel::STATUS_OPEN ? 'open' : 'closed';
            $metrics[$key]['time_spent'] = (float) $row['time_spent'];
            $metrics[$key]['time_estimated'] = (float) $row['time_estimated'];
        }

        return $metrics;
    }
}
