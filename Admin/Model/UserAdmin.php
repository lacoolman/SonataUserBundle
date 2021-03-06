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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            ->add('firstname', null, ['label' => 'Имя'])
            ->add('lastname', null, ['label' => 'Фамилия'])
            ->add('position', null, ['label' => 'Должность'])
            ->add('username', null, ['label' => 'Имя пользователя'])
            ->add('email', null, ['label' => 'E-mail'])
            ->add('groups', 'string', ['label' => 'Группы', 'template' => 'SonataAdminBundle::CRUD/list__groups_filter.html.twig'])
            ->add('enabled', null, ['label' => 'Доступ'])

//            ->add('locked')
            ->add('createdAt', null, ['label' => 'Дата создания'])
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
//            ->add('locked')
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
            ->with('Пользователь')
                ->add('username')
                ->add('email', null, array('label' => 'E-mail'))
                ->add('groups')
            ->end()
            ->with('Профиль')
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
//                ->assertRegex(['pattern' => '/[A-Za-z\d]*[A-Za-z][A-Za-z\d]*/'])
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

    }//[A-Za-z\d]*[A-Za-z][A-Za-z\d]*

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->editSelf() && !$this->user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $enabled = $this->getSubject()->getId() ? $this->getSubject()->isEnabled() : true;
        $require = $this->getSubject()->getId() ? '' : ' *';
        $this->getSubject()->setEnabled($enabled);
        
        $formMapper
            ->with('Пользователь')
                ->add('username', null, array('read_only' => !$this->user->hasRole('ROLE_ADMIN')))
                ->add('email', null, array('label' => 'E-mail'))
                ->add('plainPassword', 'repeated', array(
                    'required' => false,
                    'first_name' => 'password',
                    'second_name' => 'confirm_password',
                    'type' => 'password',
                    'invalid_message' => 'Пароли должны совпадать',
                    'error_bubbling' => false,
                    'options' => array('label' => 'Пароль' . $require)
                )
            );
        $formMapper->get('plainPassword')->get('confirm_password')->setAttribute('label', 'Повторите пароль' . $require);
        if ($this->user->hasRole('ROLE_ADMIN')) {
            $formMapper
                ->with('Пользователь')
                ->add('groups', 'sonata_type_model', array('required' => true));
        }
        $formMapper
//                ->add('dateOfBirth', 'date', array('required' => false))
                ->add('firstname', null, array('required' => false))
                ->add('lastname', null, array('required' => false))
                ->add('position', null, ['label' => 'Должность'])
                ->add('website', 'url', array('required' => false))
//                ->add('biography', 'text', array('required' => false))
//                ->add('gender', null, array('required' => false))
//                ->add('locale', null, array('required' => false))
//                ->add('timezone', null, array('required' => false))
                ->add('phone', null, array('required' => false))
        ;

        if (!$this->getSubject()->hasRole('ROLE_SUPER_ADMIN') && $this->user->hasRole('ROLE_ADMIN')) {
            $formMapper
//                ->add('roles', 'sonata_security_roles', array(
//                    'expanded' => true,
//                    'multiple' => true,
//                    'required' => false
//                ))
//                ->add('locked', null, array('required' => false))
//                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false, 'label' => 'Доступ'))
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

    public function preRemove($object)
    {
        parent::preRemove($object);
        foreach ($object->getEvents() as $event) {
            $event->setUser(null);
        }

        foreach ($object->getInputReports() as $report) {
            $report->removeUserAllowed($object);
        }
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

    private function editSelf() {
        $this->user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
        return $this->user->getId() == $this->getSubject()->getId();
    }

    public function getExportFields()
    {
        $exportFields = parent::getExportFields();
        unset($exportFields[array_search('groups', $exportFields)]);
        $exportFields['Группы'] = 'groupLabels';

        return $exportFields;
    }

    protected $label = 'Пользователи';

    /**
     * @var \Sonata\UserBundle\Entity\BaseUser $user
     */
    protected $user;
}
