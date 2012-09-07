<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    
    public function indexAction()
    {
        $view = new ViewModel();
        
        $form = new \Application\Form\Protect('aaaa');
        $form->add(array(
            'type' => '\Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Email',
                'validators' => array(
                    '\Zend\Validator\EmailAddress' => array()
                ),
                'required' => true
            ),
            'attributes' => array(
                'class' => 'input-xlarge'
            )
        ));
        $form->add(array(
            'type' => '\Zend\Form\Element\Password',
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
                'required' => true
            ),
            'attributes' => array(
                'class' => 'input-xlarge'
            )
        ));
        $form->add(array(
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'submit',
            'options' => array(
                
            ),
            'attributes' => array(
                'value' => 'Login',
                'class' => 'btn btn-primary btn-large left'
            )
        ), array('priority' => -10));
        $form->add(array(
            'type' => '\Zend\Form\Element\Button',
            'name' => 'reset',
            'options' => array(
                
            ),
            'attributes' => array(
                'value' => 'Reset',
                'class' => 'btn btn-primary btn-large right'
            )
        ), array('priority' => -10));
                        
        ////// ZF1 like
        $filter = new \Zend\InputFilter\BaseInputFilter();
        foreach($form->getIterator() as $element){
            $e = new \Zend\InputFilter\Input($element->getName());
            $e->setRequired( ($element->getOption('required')) ? true : false );
            
            $validators = new \Zend\Validator\ValidatorChain();
            if(!is_null($element->getOption('validators'))){
                foreach($element->getOption('validators') as $k => $v){
                    $validators->addByName($k, $v);
                }
            }
            if($element instanceof \Zend\Form\Element\Csrf){
                $csrf = $element->getInputSpecification();
                $validators->addValidator($csrf['validators'][0]);
            }
            $e->setValidatorChain($validators);
            
            $filters = new \Zend\Filter\FilterChain();
            if(!is_null($element->getOption('filters'))){
                foreach($element->getOption('filters') as $k => $v){
                    $filters->attachByName($k, $v);
                }
            }
            $e->setFilterChain($filters);
                        
            $filter->add($e);
        }
        $form->setInputFilter($filter);
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                
            }
        }
        
        $view->form = $form;
        return $view;
    }
    
    public function init() {
        $this->layout('layout/login');
    }
}
