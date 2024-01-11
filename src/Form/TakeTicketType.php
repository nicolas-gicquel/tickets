<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TakeTicketType extends AbstractType
{

    public function __construct(private EntityManagerInterface $entityManager, private Security $security)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EntityType::class, [
                'data' => $this->entityManager->getReference(Status::class, 2),
                'class' => Status::class,
                'attr' => ['class' => 'd-none'],
                'choice_label' => 'label',
                'label' => false
            ])
            ->add('button', SubmitType::class, [
                'attr' => ['class' => 'btn btn-second'],
                'label' => 'Prendre le ticket'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
