<?php

namespace KriSpiX\VideothequeBundle\Form;

use KriSpiX\VideothequeBundle\Entity\Movie;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class MovieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ean',            'text')
            ->add('title',          'text')
            ->add('overview',       'textarea', array(
                'required' => false
            ))
            ->add('movieDate',      'date', array(
                'required' => false, 
                'years' => range(Date('Y') - 100, date('Y'))
            ))
            ->add('link',           'url')
            ->add('image',          'url')
            ->add('format',         'entity', array(
                'class'     => 'KriSpiXVideothequeBundle:Format',
                'property'  => 'name'
            ))
            ->add('lend',           'checkbox', array('required' => false))
            ->add('see',            'checkbox', array('required' => false))
            ->add('purchaseDate',   'date')
            ->add('keywords',       'collection', array(
                'type'          => new KeywordType(),
                'allow_add'     => true,
                'allow_delete'  => true,
                'label'         => 'Mots clés'
            ))
            ->add('genres',         'entity', array(
                'class'     => 'KriSpiXVideothequeBundle:Genre',
                'property'  => 'name',
                'multiple'  => true
            ))
            ->add('save', 'submit', array('label' => 'Enregistrer'))
        ;
        
        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (array_key_exists('keywords', $data)) {
                    foreach ($data['keywords'] as $id => $keywords) {
                        foreach ($keywords as $key => $value) {
                            $data['keywords'][$id][$key] = ucfirst(strtolower($value));
                        }
                    }
                }
                $event->setData($data);
            },
            255
        );
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'KriSpiX\VideothequeBundle\Entity\Movie'
        ));
    }
    
    public function getName()
    {
        return 'krispix_videothequebundle_movie';
    }
}
