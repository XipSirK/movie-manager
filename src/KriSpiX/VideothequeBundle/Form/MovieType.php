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
            ->add('overview',       'text', array(
                'required' => false
            ))
            ->add('movieDate',      'date', array(
                'required' => false, 
                'years' => range(Date('Y') - 100, date('Y'))
            ))
            ->add('link',   'text')
            ->add('image',          'text')
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
                // [keywords] => Array ( [130] => Array ( [name] => TOTO ) [1] => Array ( [name] => Nouveau ) )
                foreach ($data['keywords'] as $id => $keywords) {
                    foreach ($keywords as $key => $value) {
                        $data['keywords'][$id][$key] = ucfirst(strtolower($value));
                    }
                }
                $event->setData($data);
            },
            255
        );
        
        /*$formModifier = function (FormInterface $form, Movie $movie = null) {
            $title = null === $movie ? '' : 'test';
            $form->add('title','text', array('data' => $title, 'required' => false));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm());
            }
        );

        $builder->get('ean')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $movie = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $movie);
            }
        );*/
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
