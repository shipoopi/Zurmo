<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2013 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2013. All rights reserved".
     ********************************************************************************/

    /**
     * Helper class to handle portlet frame views where the metadata is being populated from outside this class.
     * This class is primarily used to display related model information in the DetailsAndRelationsView. There are
     * two ways this way handles portlets in a user interface. The first way is stacked using the JuiPortlets widget
     * while the second way is tabbed using the CJuiTabs widget.
     */
    class ModelRelationsSecuredPortletFrameView extends SecuredPortletFrameView
    {
        protected $layoutType = '100';

        private $metadata;

        private $portletsAreCollapsible;

        private $portletsAreMovable;

        private $showAsTabs;

        private $portletsAreRemovable;

        public function __construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata,
                                    $portletsAreCollapsible = true, $portletsAreMovable = true, $showAsTabs = false, $layoutType = '100',
                                    $portletsAreRemovable = true)
        {
            assert('is_string($controllerId)');
            assert('is_string($moduleId)');
            assert('is_string($uniqueLayoutId)');
            assert('is_array($params)');
            assert('is_array($metadata)');
            assert('is_bool($portletsAreCollapsible)');
            assert('is_bool($portletsAreMovable)');
            assert('is_bool($showAsTabs)');
            assert('is_bool($portletsAreRemovable)');
            $this->controllerId           = $controllerId;
            $this->moduleId               = $moduleId;
            $this->uniqueLayoutId         = $uniqueLayoutId;
            $this->params                 = $params;
            $this->metadata               = $metadata;
            $this->portletsAreCollapsible = $portletsAreCollapsible;
            $this->portletsAreMovable     = $portletsAreMovable;
            $this->showAsTabs             = $showAsTabs;
            $this->layoutType             = $layoutType;
            $this->portletsAreRemovable   = $portletsAreRemovable;
        }

        protected function renderContent()
        {
            $this->portlets = $this->getPortlets($this->uniqueLayoutId, $this->metadata);
            return $this->renderPortlets($this->uniqueLayoutId,
                                         $this->portletsAreCollapsible,
                                         $this->portletsAreMovable,
                                         $this->portletsAreRemovable);
        }

        protected function renderPortlets($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true)
        {
            if (!$this->showAsTabs)
            {
                return parent::renderPortlets($uniqueLayoutId, $portletsAreCollapsible, $portletsAreMovable, $portletsAreRemovable);
            }
            assert('is_bool($portletsAreCollapsible) && $portletsAreCollapsible == false');
            assert('is_bool($portletsAreMovable) && $portletsAreMovable == false');
            return $this->renderPortletsTabbed();
        }

        protected function renderPortletsTabbed()
        {
            assert('count($this->portlets) == 1 || count($this->portlets) == 0');
            if (count($this->portlets) == 1)
            {
                $tabItems = array();
                foreach ($this->portlets[1] as $noteUsed => $portlet)
                {
                    $tabItems[$portlet->getTitle()] = array(
                        'id'      => $portlet->getUniquePortletPageId(),
                        'content' => $portlet->renderContent()
                    );
                }
                $cClipWidget = new CClipWidget();
                $cClipWidget->beginClip("JuiTabs");
                $cClipWidget->widget('zii.widgets.jui.CJuiTabs', array(
                    'id' => $this->uniqueLayoutId . '-portlet-tabs',
                    'tabs' => $tabItems
                ));
                $cClipWidget->endClip();
                return $cClipWidget->getController()->clips['JuiTabs'];
            }
        }

        protected function arePortletsRemovable()
        {
            return true;
        }

        public function isUniqueToAPage()
        {
            return false;
        }
    }
?>