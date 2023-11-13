<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Employe;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom',
                'mapped'=>false,
                //'placeholder'=>'Entrez votre nom '
                ])  ->add('email', EmailType::class, ['label' => 'Email',
                'mapped'=>false,
               // 'placeholder'=>'Entrez votre email '
                ])
            ->add('prenoms', TextType::class, ['label' => 'Prénoms',
                'mapped'=>false,
               // 'placeholder'=>'Entrez votre prénoms '
                ])

            ->add('username', TextType::class, ['label' => 'Username',
                'mapped'=>true,
                // 'placeholder'=>'Entrez votre prénoms '
            ])
            ->add('dateNaissance',  DateType::class,  [
                'mapped' => false,
                //'placeholder'=>"Entrez votre date de naissance s'il vous plaît",
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   'format' => 'yyyy-MM-dd',
                'label' => 'Date de naissance', 'empty_data' => date('d/m/Y'), 'required' => false
            ])
            // ->add('cloture', 'choice', array('required' => false, 'label' => 'Filtrer les interventions', 'choices' => array(0 => 'Exclure les clôturées', 1 => 'Inclure les clôturées', 2 => 'Uniquement les clôturées'), 'empty_value' => false))
            //->add('lowerDate', 'date', array('label' => 'Date de début', 'required' => false, 'input' => 'string', 'empty_value' => array('year' => 'Année', 'month' => 'Mois', 'day' => 'Jour')))

            ->add(
                'password',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'passwordRequired' => false
        ]);

        $resolver->setRequired('passwordRequired');
    }
}
