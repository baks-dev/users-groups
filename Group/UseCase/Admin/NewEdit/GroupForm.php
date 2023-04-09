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

namespace BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit;

use BaksDev\Users\Groups\Group\Type\Prefix\GroupPrefix;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupDTO;
use BaksDev\Users\Groups\Role\Repository\RoleChoice\RoleChoiceInterface;
use BaksDev\Users\Groups\Role\Repository\VoterChoice\VoterChoiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GroupForm extends AbstractType
{
	
	private RoleChoiceInterface $roleChoice;
	
	
	public function __construct(RoleChoiceInterface $roleChoice)
	{
		$this->roleChoice = $roleChoice;
	}
	
	
	public function buildForm(FormBuilderInterface $builder, array $options) : void
	{
		/* TextType */
		$builder->add('group', TextType::class);
		
		$builder->get('group')->addModelTransformer(
			new CallbackTransformer(
				function($price) {
					return $price instanceof GroupPrefix ? $price->getValue() : $price;
				},
				function($price) {
					
					return new GroupPrefix($price);
				}
			)
		);
		
		$builder->add('sort', TextType::class);
		
		$builder->add('quota',
			\BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\Quota\GroupQuotaForm::class,
			['label' => false]
		);
		
		/* TRANS CollectionType */
		$builder->add('translate', CollectionType::class, [
			'entry_type' => \BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\Trans\GroupTransForm::class,
			'entry_options' => ['label' => false],
			'label' => false,
			'by_reference' => false,
			'allow_delete' => true,
			'allow_add' => true,
			'prototype_name' => '__trans__',
		]);
		
		$builder->addEventListener(
			FormEvents::PRE_SET_DATA,
			function(FormEvent $event) {
				$data = $event->getData();
				$form = $event->getForm();
				
				if($data)
				{
					foreach($this->roleChoice->get() as $role)
					{
						$isHash = array_filter($data->getRole()->toArray(), function($dataRole) use ($role) {
							return $dataRole->getRole()->getValue() === $role->getValue();
						});
						
						if(empty($isHash))
						{
							$CheckRoleDTO = new \BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckRoleDTO(
							);
							$CheckRoleDTO->setRole($role);
							$data->addRole($CheckRoleDTO);
						}
						else
						{
							$data->getRole()->get(array_key_first($isHash))->setRole($role);
							$data->getRole()->get(array_key_first($isHash))->checked();
						}
					}
				}
			}
		);
		
		/* TRANS CollectionType */
		$builder->add('role', CollectionType::class, [
			'entry_type' => \BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\CheckRole\CheckRoleForm::class,
			'entry_options' => ['label' => false, 'role' => $this->roleChoice->get()],
			'label' => false,
			'by_reference' => false,
			'allow_delete' => true,
			'allow_add' => true,
			'prototype_name' => '__role__',
			
		]);
		
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
				'data_class' => GroupDTO::class,
				'method' => 'POST',
				'attr' => ['class' => 'w-100'],
			]
		);
	}
	
}
