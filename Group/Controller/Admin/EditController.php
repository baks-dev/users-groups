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

namespace BaksDev\Users\Groups\Group\Controller\Admin;


use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\Entity\Group;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupDTO;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupForm;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupHandler;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_GROUPS_EDIT" in role_names'))]
final class EditController extends AbstractController
{
    #[Route('/admin/group/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    //#[ParamConverter('Event', GroupEvent::class)]
    public function edit(
      Request $request,
      GroupHandler $handler,
		#[MapEntity] GroupEvent $Event
    ) : Response
    {
        $GroupDTO = new GroupDTO();
        $Event->getDto($GroupDTO);

        /* Форма добавления */
        $form = $this->createForm(GroupForm::class, $GroupDTO);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $Group = $handler->handle($GroupDTO);
            
            if($Group instanceof GroupEvent)
            {
                $this->addFlash('success', 'admin.success.update', 'groups.group');
                return $this->redirectToRoute('UserGroup:admin.index');
            }
    
            $this->addFlash('danger', 'admin.danger.update', 'groups.group', $Group);
            return $this->redirectToRoute('UserGroup:admin.index');
        }
        
        return $this->render(['form' => $form->createView()]);
        
    }

}