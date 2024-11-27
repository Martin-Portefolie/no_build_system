<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('name', TextType::class, [
'label' => 'ClientFixtures Name',
'attr' => ['class' => 'form-input']
])
->add('contactEmail', EmailType::class, [
'label' => 'Contact Email',
'attr' => ['class' => 'form-input']
])
->add('contactPhone', TelType::class, [
'label' => 'Contact Phone',
'attr' => ['class' => 'form-input']
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Client::class,
]);
}
}
