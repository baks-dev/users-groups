<?php
/*
 * Copyright (c) 2023.  Baks.dev <admin@baks.dev>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BaksDev\Users\Groups\Users\UseCase\Admin\Add;

use BaksDev\Users\Groups\Group\Repository\ChoiceGroups\ChoiceGroupsInterface;
use BaksDev\Users\Groups\Users\Repository\UsersGroupChoiceForm\UsersGroupChoiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CheckUsersForm extends AbstractType
{
	private UsersGroupChoiceInterface $choiceUsers;
	
	private ChoiceGroupsInterface $choiceGroups;
	
	
	public function __construct(UsersGroupChoiceInterface $choiceUsers, ChoiceGroupsInterface $choiceGroups)
	{
		$this->choiceUsers = $choiceUsers;
		$this->choiceGroups = $choiceGroups;
	}
	
	
	public function buildForm(FormBuilderInterface $builder, array $options) : void
	{
		/* TextType */
		
		$builder->add(
			'user', ChoiceType::class,
			[
				'choices' => $this->choiceUsers->get(),
				'choice_value' => function($users) {
					return $users?->getValue();
				},
				'choice_label' => function($users) {
					return $users->getName();
				},
				'multiple' => false,
				'expanded' => false,
				'label' => false,
				'required' => true,
			]
		);
		
		$builder->add(
			'group', ChoiceType::class,
			[
				'choices' => $this->choiceGroups->get(),
				'choice_value' => function($group) {
					return $group?->getValue();
				},
				'choice_label' => function($group) {
					return $group->getName();
				},
				'multiple' => false,
				'expanded' => false,
				'label' => false,
				'required' => true,
			]
		);
		
		/* Сохранить ******************************************************/
		$builder->add
		(
			'Save',
			SubmitType::class,
			['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
		);
		
	}
	
	
	public function configureOptions(OptionsResolver $resolver) : void
	{
		$resolver->setDefaults
		(
			[
				'data_class' => CheckUsersDTO::class,
				'method' => 'POST',
				'attr' => ['class' => 'w-100'],
			]
		);
	}
	
}
