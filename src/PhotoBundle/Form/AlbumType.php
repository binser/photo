<?php

namespace PhotoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlbumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('caption', array('label' => 'Название альбома'))
            ->add('url', array('label' => 'Интернет адрес альбома'))
            ->add('enabled', array('label' => 'Статус альбома'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PhotoBundle\Entity\Album'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'photobundle_album';
    }
}
