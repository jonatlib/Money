<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class AjaxController extends AbstractActionController {

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $auth;
    protected $userId;

    /**
     * @var \Application\Model\Money
     */
    protected $model;

    public function indexAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthCategorySummary();
        return $view;
    }

    public function summaryAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthSumary();
        return $view;
    }

    public function moneyAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthMoney();
        return $view;
    }

    public function spendingAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthSpending();
        return $view;
    }

    public function earningAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthEarning();
        return $view;
    }

    public function spendingcategoryAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthSpendingByCategory();
        return $view;
    }

    public function linegraphAction() {
        $view = new JsonModel();
        $data = array('Date' => array('Date'));

        $d = $this->model->getMonthSpendingByCategory()->toArray();
        foreach ($d as $val) {
            if (!in_array($val['categName'], $data['Date']))
                $data['Date'][] = $val['categName'];
        }
        foreach ($d as $val) {
            $index = array_search($val['categName'], $data['Date']);
            if (empty($data[$val['date']])) {
                foreach ($data['Date'] as $i => $v)
                    $data[$val['date']][$i] = 0;
                $data[$val['date']][0] = $val['date'];
            }
            $data[$val['date']][$index] = (int) abs($val['sumary']);
        }
        $view->data = array_values($data);
        return $view;
    }

    public function dictionaryAction() {
        $view = new JsonModel();
        /* @var $translator \Application\Library\I18n\Translator\Translator */
        $translator = $this->serviceLocator->get('translator');

        //FIXME get dictionary from translator
        $locale = $translator->getLocale();
        $dictionary = function()use($locale) {
                    return require __DIR__ . "/../../../language/{$locale}.php";
                };
        $view->dictionary = $dictionary();
        return $view;
    }

    public function translateAction() {
        $view = new JsonModel();
        $get = $this->params()->fromQuery('text', null);
        if (!empty($get)) {
            $translator = $this->serviceLocator->get('translator');
            if (is_array($get)) {
                $result = array();
                foreach ($get as $g) {
                    $result[] = $translator->translate($g);
                }
                $view->text = $result;
            } else {
                $view->text = $translator->translate($get);
            }
        } else {
            $view->text = 'unknown';
        }

        return $view;
    }

    public function datestartAction() {
        $view = new JsonModel();
        /* @var $model \Application\Model\UserVars */
        $model = $this->getServiceLocator()->get('\Application\Model\UserVars');
        $view->date = $model->getVariable('date-start');
        if($view->date === false){
            $view->date = time() - 24 * 3600;
        }
        return $view;
    }

    public function datestopAction() {
        $view = new JsonModel();
        /* @var $model \Application\Model\UserVars */
        $model = $this->getServiceLocator()->get('\Application\Model\UserVars');
        $view->date = $model->getVariable('date-stop');
        if($view->date === false){
            $view->date = time();
        }
        return $view;
    }

    public function datesaveAction() {
        $view = new JsonModel();
        $view->result = 'error';
        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            $start = $data['start'];
            $stop = $data['stop'];

            /* @var $model \Application\Model\UserVars */
            $model = $this->getServiceLocator()->get('\Application\Model\UserVars');
            $model->setVariable('date-set', time());
            $model->setVariable('date-start', $start);
            $model->setVariable('date-stop', $stop);

            $view->result = 'success';
        }
        return $view;
    }

    public function init() {
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->userId = $this->auth->getIdentity()->id;
        $this->model = $this->getServiceLocator()->get('\Application\Model\Money');
    }

}
