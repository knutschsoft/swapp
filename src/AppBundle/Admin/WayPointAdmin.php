<?php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class WayPointAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('walk')
            ->add('locationName')
            ->add('ageRangeStart')
            ->add('ageRangeEnd')
            ->add('malesCount')
            ->add('femalesCount')
            ->add('note', null, array('required' => false));
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
            ->add('walk')
            ->add('locationName')
            ->add('ageRangeStart')
            ->add('ageRangeEnd')
            ->add('malesCount')
            ->add('femalesCount')
            ->add('note');
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
            ->add('walk')
            ->addIdentifier('locationName')
            ->add('ageRangeStart')
            ->add('ageRangeEnd')
            ->add('malesCount')
            ->add('femalesCount')
            ->add('note');
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
