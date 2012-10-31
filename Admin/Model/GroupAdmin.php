<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Admin\Model;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class GroupAdmin extends Admin
{
    protected $formOptions = array(
        'validation_groups' => 'Registration'
    );

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', array());
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => '№'])
            ->addIdentifier('name')
            ->add('roles')
            ->add('_action', 'actions', [
                'label' => 'Действия',
                'actions' => [
    //                    'view' => [],
                    'edit' => [],
                ]
            ])
        ;
    }

    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('roles', 'sonata_security_roles', array(
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ))
        ;
    }
}
