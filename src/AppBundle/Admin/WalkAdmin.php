<?php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class WalkAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('wayPoints')
            ->add('startTime', null, array('required' => false))
            ->add('endTime', null, array('required' => false))
            ->add('walkReflection', null, array('required' => false))
            ->add('walkTeamMembers', null, array('required' => false))
            ->add('tags', null, array('required' => false))
            ->add('rating', null, array('required' => false))
            ->add('systemicQuestion', null, array('required' => false))
            ->add('systemicAnswer', null, array('required' => false));
    }

    /**
     * @param DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('wayPoints')
            ->add('startTime')
            ->add('endTime')
            ->add('walkReflection')
            ->add('walkTeamMembers')
            ->add('tags')
            ->add('rating')
            ->add('systemicQuestion')
            ->add('systemicAnswer');
    }

    /**
     * @param ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('wayPoints')
            ->add('walkReflection')
            ->add('tags');
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     *
     * @return void
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
            ->end();
    }
}
