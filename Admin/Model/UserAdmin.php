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
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use \IronSoft\Analytics\ISBundle\Admin\AbstractAdmin;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends AbstractAdmin
{
//    protected $formOptions = array(
//        'validation_groups' => 'Profile'
//    );

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username')
            ->add('email')
            ->add('groups', 'string', ['template' => 'SonataAdminBundle::CRUD/list__groups_filter.html.twig'])
            ->add('enabled')
            ->add('locked')
            ->add('createdAt')
            ->add('_action', 'actions', ['label' => 'Действия',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ])
        ;

//        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
//            $listMapper
//                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
//            ;
//        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('locked')
            ->add('email')
            ->add('groups')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Справочник пользователей')
                ->add('username')
                ->add('email')
            ->end()
            ->with('')
                ->add('groups')
            ->end()
            ->with('Profile')
//                ->add('dateOfBirth', 'date')
                ->add('firstname')
                ->add('lastname')
                ->add('website')
//                ->add('biography')
//                ->add('gender')
//                ->add('locale')
//                ->add('timezone')
                ->add('phone')
            ->end()
//            ->with('Social')
//                ->add('facebookUid')
//                ->add('facebookName')
//                ->add('twitterUid')
//                ->add('twitterName')
//                ->add('gplusUid')
//                ->add('gplusName')
//            ->end()
//            ->with('Security')
//                ->add('token')
//                ->add('twoStepVerificationCode')
//            ->end();
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {;
        $errorElement
            ->with('groups')
            ->assertSonataModel()
            ->end()
            ->with('username')
            ->assertNotBlank()
            ->end()
            ->with('email')
            ->assertEmail()
            ->assertNotBlank()
            ->end();
        if (!$object->getId()) {
            $errorElement
                ->with('plainPassword')
                ->assertNotBlank()
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Справочник пользователя')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'password', array('required' => false))
                ->add('groups', 'sonata_type_model', array('required' => true))
//                ->add('dateOfBirth', 'date', array('required' => false))
                ->add('firstname', null, array('required' => false))
                ->add('lastname', null, array('required' => false))
                ->add('website', 'url', array('required' => false))
//                ->add('biography', 'text', array('required' => false))
//                ->add('gender', null, array('required' => false))
//                ->add('locale', null, array('required' => false))
//                ->add('timezone', null, array('required' => false))
                ->add('phone', null, array('required' => false))
        ;

        if (!$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
//                ->add('roles', 'sonata_security_roles', array(
//                    'expanded' => true,
//                    'multiple' => true,
//                    'required' => false
//                ))
                ->add('locked', null, array('required' => false))
//                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
//                ->add('credentialsExpired', null, array('required' => false))
            ;
        }

//        $formMapper
//            ->with('Security')
//                ->add('token', null, array('required' => false))
//                ->add('twoStepVerificationCode', null, array('required' => false))
//            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    public function getClassnameLabel() {
        return 'Пользователи';
    }

    protected $label = 'Пользователи';
}
