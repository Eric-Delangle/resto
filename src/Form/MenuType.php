<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Order;
use App\Form\OrderType;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom du menu'])
            ->add('entree', TextType::class)
            ->add('plat', TextType::class)
            ->add('fromage', TextType::class)
            ->add('dessert', TextType::class)
            ->add('boisson', TextType::class)

            ->add('price', TextType::class, ['label' => 'Prix']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'translation_domain' => 'forms',

        ]);
    }
}
