<?php
Yii::import('zii.widgets.CPortlet');
 
class UserMenu extends CPortlet
{
    public function init()
    {
        $this->title=CHtml::encode(Yii::app()->user->name);
        parent::init();
    }
    
    protected function renderContent()
    {
        $this->render('userMenu');
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
