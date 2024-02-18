<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Member;
use App\Entity\MemberPresence;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImportFromItacType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options): void {
    $builder
      ->add('csv', FileType::class, [
        'label' => 'Fichier (CSV)',
        'mapped' => false,
        'required' => true,
        'constraints' => [
          new File([
            'mimeTypes' => [
              'text/csv'
            ],
            'mimeTypesMessage' => 'Le fichier doit être un CSV'
          ])
        ]
      ])
      ->add('Importer', SubmitType::class);

  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([]);
  }
}
