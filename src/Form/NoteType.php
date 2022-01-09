<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
              'attr' => [
                'placeholder' => 'Titre',
              ],
            ])
            ->add('content', TextareaType::class, [
              'attr' => [
                'placeholder' => 'Text..',
                'rows' => 30,
              ],
            ])
            ->add('categories', EntityType::class, [
              'class' => Category::class,
              'choice_label' => 'name',
              'required' => false,
              'multiple' => true,
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
              $note = $event->getData();
              $form = $event->getForm();

              if (!$note) {
                return;
              }

              // //overwritte entitytype
              // $form->add('categories', EntityType::class, [
              //   'class' => Category::class,
              //   'choice_label' => 'name',
              //   'required' => false,
              //   'multiple' => true,
              // ]);
            })
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
