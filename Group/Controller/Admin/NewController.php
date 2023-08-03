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

namespace BaksDev\Users\Groups\Group\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Users\Groups\Group\Entity\Event\GroupEvent;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupDTO;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupForm;
use BaksDev\Users\Groups\Group\UseCase\Admin\NewEdit\GroupHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_GROUPS_NEW')]
final class NewController extends AbstractController
{
    #[Route('/admin/group/new', name: 'admin.newedit.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        GroupHandler $handler,
    ): Response {
        $GroupDTO = new GroupDTO();

        // Форма добавления
        $form = $this->createForm(GroupForm::class, $GroupDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Group = $handler->handle($GroupDTO);

            if ($Group instanceof GroupEvent) {
                $this->addFlash('success', 'admin.success.new', 'groups.group');

                return $this->redirectToRoute('UserGroup:admin.index');
            }

            $this->addFlash('danger', 'admin.danger.new', 'groups.group', $Group);

            return $this->redirectToRoute('UserGroup:admin.index');
        }

        return $this->render(['form' => $form->createView()]);
    }
}
