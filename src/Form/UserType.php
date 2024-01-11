<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     ->add('email')
        //     ->add('roles')
        //     ->add('password')
        //     ->add('firstName')
        //     ->add('lastName')
        //     ->add('pseudo')
        // ;
        $userRoles = $options['user_roles'];
        if (in_array('ROLE_ADMIN', $userRoles)) {
            $builder
                
                ->add('pseudo')
                ->add('firstName')
                ->add('lastName')
                ->add('roles', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'choices'  => [
                        'Utilisateur' => 'ROLE_USER',
                        'Editeur' => 'ROLE_EDITER',
                        'Super Editeur' => 'ROLE_SUPER_EDITER',
                        'Administrateur' => 'ROLE_ADMIN',
                    ],
                ])
                ->add('discordPath')
                ->add('avatar', FileType::class, [
                    'label' => 'Avatar (Image file)',
                    'mapped' => false, 
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/jpg',
                            ],
                            'mimeTypesMessage' => 'Mettez une image valide (jpeg,jpg, png, gif)',
                        ])
                    ],
                ]);
        } else {
            $builder
                ->add('email')
                ->add('pseudo')
                ->add('discordPath')
                ->add('avatar', FileType::class, [
                    'label' => 'Photo de l’article',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '5000k',
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Image trop lourde',
                        ])
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user_roles' => [], 
        ]);
    }
}
