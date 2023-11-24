<?php

namespace App\Form;

use App\Entity\Advert;
use LogicException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\WorkflowInterface;

class AdvertEdit extends AbstractType
{
    private const TRANSITIONS = [
        'draft' => ['published'=> 'publish', 'rejected'=>'reject'],
        'published' => ['rejected'=>'reject_publish'],
        'rejected' => [],
    ];
    public function __construct(private WorkflowInterface $advertWorkflow) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('state', ChoiceType::class, [
            'choices' => [
                'Draft' => 'draft',
                'Published' => 'published',
                'Rejected' => 'rejected',
            ],
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $advert = $form->getData();

            try {
                $currentState = $advert->getState();
                $targetState = $data['state'];

                if (array_key_exists($targetState, self::TRANSITIONS[$currentState])) {
                    $this->advertWorkflow->apply($advert, self::TRANSITIONS[$currentState][$targetState]);

                    if ($targetState === 'published') {
                        $advert->setPublishedAt(new \DateTime());
                    } elseif ($targetState === 'rejected' && $currentState === 'published') {
                        $advert->setPublishedAt(null);
                    }
                } else {
                    throw new LogicException('Invalid state transition.');
                }
            } catch (LogicException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class
        ]);
    }
}
