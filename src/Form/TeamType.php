<?php

namespace App\Form;

use App\Entity\Sponsors;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('creationDate', ChoiceType::class, [
                'choices' => $this->getYearsRange(),
            ])
            ->add('coach')
            ->add('nationalTitle')
            ->add('point')
            ->add('HomeGames')
            ->add('AwayGames')
           // ->add('teamSponsors')

        ;
    }
    private function getYearsRange()
    {
        $currentYear = (int) date('Y');
        $yearsRange = range(1900, $currentYear);

        return array_combine($yearsRange, $yearsRange);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
