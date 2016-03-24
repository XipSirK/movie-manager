<?php

namespace KriSpiX\VideothequeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MovieEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('updatedAt');
    }
    
    public function getName()
    {
        return 'krispix_videothequebundle_movie_edit';
    }
    
    public function getParent()
    {
        return new MovieType();
    }
}
