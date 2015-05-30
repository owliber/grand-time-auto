<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
        
        public function end(){
            Yii::app()->db->setActive(false);
            gc_collect_cycles(); //Free up memory resources
        }
        
        public function init(){
            Yii::app()->onEndRequest = array('Controller','end');
        }
        
        public function initialize()
        {
          if(!Yii::app()->user->hasAccess() && !Yii::app()->user->isAdmin())
          {
              $url = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
              $this->redirect(array('site/invalid','url'=>$url), true) ;
          }
        }
        
}