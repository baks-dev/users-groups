<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole;

use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckRoleDTO;
use BaksDev\Users\Groups\Role\Repository\RoleChoice\RoleChoiceInterface;
use BaksDev\Users\Groups\Role\Repository\VoterChoice\VoterChoiceInterface;
use BaksDev\Users\Groups\Role\Type\RolePrefix\RolePrefix;
use BaksDev\Users\Groups\Role\Type\VoterPrefix\VoterPrefix;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CheckRoleForm extends AbstractType
{
	private VoterChoiceInterface $voterChoice;
	
	
	public function __construct(VoterChoiceInterface $voterChoice)
	{
		$this->voterChoice = $voterChoice;
	}
	
	
	public function buildForm(FormBuilderInterface $builder, array $options) : void
	{
		//$builder->add('role');
		
		//        $builder->addEventListener(
		//          FormEvents::POST_SUBMIT,
		//          function (FormEvent $event) use ($options)
		//          {
		//              $data = $event->getData();
		//              $form = $event->getForm();
		//
		//              dump($data);
		//
		//              //$form->add('checked_role', CheckboxType::class, ['mapped' => false]);
		//
		//
		//              //if($form->get('checked')->getData());
		//
		//
		//              //dump($form->get('checked')->getData());
		//          });
		//
		$builder->addEventListener(
			FormEvents::PRE_SET_DATA,
			function(FormEvent $event) {
				$data = $event->getData();
				$form = $event->getForm();
				
				if($data)
				{
					
					$form->add(
						'checked', CheckboxType::class,
						[
							'required' => false,
							'label' => $data->getRole()->getName(),
							//'false_values' => ['null'],
							'empty_data' => false,
							'attr' => ['class' => 'group-role-checked'],
						]
					);
					
					/* $form
					  ->add('role', ChoiceType::class, [
						'choices' => $options['role'], //UserProfileStatus::cases(),
						'choice_value' => function (?RolePrefix $role)
						{
							return $role?->getValue();
						},
						'choice_label' => function (RolePrefix $role)
						{
							return $role->getName();
						},
						'label' => false,
						'expanded' => false,
						'multiple' => false,
						'required' => true,
						'attr' => ['data-select' => 'select2',]
					  ]);*/
					
					/*$form
					  ->add('voter', ChoiceType::class, [
						'choices' => $this->voterChoice->get($data->getRole()), //UserProfileStatus::cases(),
						'choice_value' => function (?VoterPrefix $voter)
						{
							return $voter?->getValue();
						},
						'choice_label' => function (VoterPrefix $voter)
						{
							return $voter->getName();
						},
						'label' => false,
						'expanded' => false,
						'multiple' => false,
						'required' => false,
					  ]);*/
					
					$votersChoice = [];
					foreach($this->voterChoice->get($data->getRole()) as $item)
					{
						$CheckVoterDTO = new \BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckVoter\CheckVoterDTO(
						);
						$CheckVoterDTO->setVoter($item);
						$votersChoice[$item->getValue()] = $CheckVoterDTO;
					}
					
					$form->add(
						'voter', ChoiceType::class,
						[
							
							'choices' => $votersChoice,
							'choice_value' => function($voter) {
								return $voter->getVoter()->getValue();
							},
							
							'choice_label' => function($voter) {
								return $voter->getVoter()->getName();
							},
							
							//                               'choice_attr' => function ($choice, $key, $value) {
							//                                   return ['checked' => $choice->getValue() == $value];
							//                               },
							
							'multiple' => true,
							'expanded' => true,
							'label' => false,
							'required' => false,
							'attr' => ['class' => 'rights_list w-100 d-flex flex-wrap ms-5 my-2 gap-3'],
						]
					);
				}
			}
		);
		
	}
	
	
	public function configureOptions(OptionsResolver $resolver) : void
	{
		$resolver->setDefaults
		(
			[
				'data_class' => CheckRoleDTO::class,
				'role' => null,
			]
		);
	}
	
}
