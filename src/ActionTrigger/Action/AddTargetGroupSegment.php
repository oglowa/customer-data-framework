<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace CustomerManagementFrameworkBundle\ActionTrigger\Action;

use CustomerManagementFrameworkBundle\ActionTrigger\Event\TargetGroupAssigned;
use CustomerManagementFrameworkBundle\ActionTrigger\RuleEnvironmentInterface;
use CustomerManagementFrameworkBundle\Model\CustomerInterface;

class AddTargetGroupSegment extends AddTrackedSegment
{
    protected $name = 'AddTargetGroupSegment';

    public function process(
        ActionDefinitionInterface $actionDefinition,
        CustomerInterface $customer,
        RuleEnvironmentInterface $environment
    )
    {
        $segmentManager = \Pimcore::getContainer()->get('cmf.segment_manager');

        $targetGroupAssigned = $environment->get(TargetGroupAssigned::STORAGE_KEY);
        if (null === $targetGroupAssigned) {
            return;
        }

        //get segment based on target group
        $segments = $segmentManager->getSegments();
        $segments->setCondition("targetGroup = ?", $targetGroupAssigned['targetGroupId']);
        $segments->load();

        if($segments->getObjects()) {
            foreach($segments as $segment) {
                $this->addSegment($segmentManager, $actionDefinition, $customer, $segment);
            }
        } else {
            return;
        }
    }

}
