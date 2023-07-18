<?php

namespace App\Form;

use App\Entity\Matches;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score1')
            ->add('score2')
//            ->add('date', DateTimeType::class,[
//                'years' => range(date('Y') - 1, date('Y') + 1),
//                'months' => range(1, 12),
//                'days' => range(1, 31),
//                'hours' => range(10, 23),
//               'minutes'=>['00','30'],
//    ])
            ->add('team1')
            ->add('team2')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matches::class,
        ]);
    }
}
